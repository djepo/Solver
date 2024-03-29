/*
   +----------------------------------------------------------------------+
   | Twig Extension                                                       |
   +----------------------------------------------------------------------+
   | Copyright (c) 2011 Derick Rethans                                    |
   +----------------------------------------------------------------------+
   | Redistribution and use in source and binary forms, with or without   |
   | modification, are permitted provided that the conditions mentioned   |
   | in the accompanying LICENSE file are met (BSD, revised).             |
   +----------------------------------------------------------------------+
   | Author: Derick Rethans <derick@derickrethans.nl>                     |
   +----------------------------------------------------------------------+
 */

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_twig.h"
#include "ext/standard/php_string.h"
#include "ext/standard/php_smart_str.h"

#include "Zend/zend_object_handlers.h"
#include "Zend/zend_interfaces.h"
#include "Zend/zend_exceptions.h"

#ifndef Z_ADDREF_P
#define Z_ADDREF_P(pz)                (pz)->refcount++
#endif

ZEND_BEGIN_ARG_INFO_EX(twig_template_get_attribute_args, ZEND_SEND_BY_VAL, ZEND_RETURN_VALUE, 6)
	ZEND_ARG_INFO(0, template)
	ZEND_ARG_INFO(0, object)
	ZEND_ARG_INFO(0, item)
	ZEND_ARG_INFO(0, arguments)
	ZEND_ARG_INFO(0, type)
	ZEND_ARG_INFO(0, isDefinedTest)
ZEND_END_ARG_INFO()

zend_function_entry twig_functions[] = {
	PHP_FE(twig_template_get_attributes, twig_template_get_attribute_args)
	{NULL, NULL, NULL}
};


zend_module_entry twig_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
	STANDARD_MODULE_HEADER,
#endif
	"twig",
	twig_functions,
	PHP_MINIT(twig),
	PHP_MSHUTDOWN(twig),
	PHP_RINIT(twig),
	PHP_RSHUTDOWN(twig),
	PHP_MINFO(twig),
#if ZEND_MODULE_API_NO >= 20010901
	PHP_TWIG_VERSION,
#endif
	STANDARD_MODULE_PROPERTIES
};


#ifdef COMPILE_DL_TWIG
ZEND_GET_MODULE(twig)
#endif

PHP_INI_BEGIN()
PHP_INI_END()

PHP_MINIT_FUNCTION(twig)
{
	REGISTER_INI_ENTRIES();

	return SUCCESS;
}


PHP_MSHUTDOWN_FUNCTION(twig)
{
	UNREGISTER_INI_ENTRIES();

	return SUCCESS;
}



PHP_RINIT_FUNCTION(twig)
{
	return SUCCESS;
}



PHP_RSHUTDOWN_FUNCTION(twig)
{
	return SUCCESS;
}


PHP_MINFO_FUNCTION(twig)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "Twig support", "enabled");
	php_info_print_table_row(2, "Version", PHP_TWIG_VERSION);
	php_info_print_table_end();

	DISPLAY_INI_ENTRIES();

}

int TWIG_ARRAY_KEY_EXISTS(zval *array, char* key, int key_len)
{
	if (Z_TYPE_P(array) != IS_ARRAY) {
		return 0;
	}
	return zend_symtable_exists(Z_ARRVAL_P(array), key, key_len + 1);
}

int TWIG_INSTANCE_OF(zval *object, zend_class_entry *interface TSRMLS_DC)
{
	if (Z_TYPE_P(object) != IS_OBJECT) {
		return 0;
	}
	return instanceof_function(Z_OBJCE_P(object), interface TSRMLS_CC);
}

int TWIG_INSTANCE_OF_USERLAND(zval *object, char *interface TSRMLS_DC)
{
	zend_class_entry **pce;
	if (Z_TYPE_P(object) != IS_OBJECT) {
		return 0;
	}
	if (zend_lookup_class(interface, strlen(interface), &pce TSRMLS_CC) == FAILURE) {
		return 0;
	}
	return instanceof_function(Z_OBJCE_P(object), *pce TSRMLS_CC);
}

zval *TWIG_GET_ARRAYOBJECT_ELEMENT(zval *object, zval *offset TSRMLS_DC)
{
    zend_class_entry *ce = Z_OBJCE_P(object);
    zval *retval;

	if (Z_TYPE_P(object) == IS_OBJECT) {
		SEPARATE_ARG_IF_REF(offset);
		zend_call_method_with_1_params(&object, ce, NULL, "offsetget", &retval, offset);

        zval_ptr_dtor(&offset);

        if (!retval) {
            if (!EG(exception)) {
                zend_error(E_ERROR, "Undefined offset for object of type %s used as array", ce->name);
            }
            return NULL;
        }

        return retval;
	}
	return NULL;
}

int TWIG_ISSET_ARRAYOBJECT_ELEMENT(zval *object, zval *offset TSRMLS_DC)
{
	zend_class_entry *ce = Z_OBJCE_P(object);
	zval *retval;

	if (Z_TYPE_P(object) == IS_OBJECT) {
		SEPARATE_ARG_IF_REF(offset);
		zend_call_method_with_1_params(&object, ce, NULL, "offsetexists", &retval, offset);

		zval_ptr_dtor(&offset);

		if (!retval) {
			if (!EG(exception)) {
				zend_error(E_ERROR, "Undefined offset for object of type %s used as array", ce->name);
			}
			return 0;
		}

		return (retval && Z_TYPE_P(retval) == IS_BOOL && Z_LVAL_P(retval));
	}
	return 0;
}

char *TWIG_STRTOLOWER(const char *str, int str_len)
{
	char *item_dup;

	item_dup = estrndup(str, str_len);
	php_strtolower(item_dup, str_len);
	return item_dup;
}

zval *TWIG_CALL_USER_FUNC_ARRAY(zval *object, char *function, zval *arguments TSRMLS_DC)
{
	zend_fcall_info fci;
	zval ***args = NULL;
	int arg_count = 0;
	HashTable *table;
	HashPosition pos;
	int i = 0;
	zval *retval_ptr;
	zval *zfunction;

	if (arguments) {
		table = HASH_OF(arguments);
		args = safe_emalloc(sizeof(zval **), table->nNumOfElements, 0);

		zend_hash_internal_pointer_reset_ex(table, &pos);

		while (zend_hash_get_current_data_ex(table, (void **)&args[i], &pos) == SUCCESS) {
			i++;
			zend_hash_move_forward_ex(table, &pos);
		}
		arg_count = table->nNumOfElements;
	}

	MAKE_STD_ZVAL(zfunction);
	ZVAL_STRING(zfunction, function, 1);
	fci.size = sizeof(fci);
	fci.function_table = EG(function_table);
	fci.function_name = zfunction;
	fci.symbol_table = NULL;
#if PHP_VERSION_ID >= 50300
	fci.object_ptr = object;
#else
	fci.object_pp = &object;
#endif
	fci.retval_ptr_ptr = &retval_ptr;
	fci.param_count = arg_count;
	fci.params = args;
	fci.no_separation = 0;

	if (zend_call_function(&fci, NULL TSRMLS_CC) == FAILURE) {
		zval_dtor(zfunction);
		efree(zfunction);
		zend_throw_exception_ex(zend_exception_get_default(TSRMLS_C), 0 TSRMLS_CC, "Could not execute %s::%s()", zend_get_class_entry(object TSRMLS_CC)->name, function TSRMLS_CC);
	}

	if (args) {
		efree(fci.params);
	}
	zval_dtor(zfunction);
	efree(zfunction);
	return retval_ptr;
}

int TWIG_CALL_BOOLEAN(zval *object, char *functionName TSRMLS_DC)
{
	zval *ret;
	int   res;

	ret = TWIG_CALL_USER_FUNC_ARRAY(object, functionName, NULL TSRMLS_CC);
	res = Z_LVAL_P(ret);
	zval_ptr_dtor(&ret);
	return res;
}

zval *TWIG_GET_STATIC_PROPERTY(zval *class, char *prop_name TSRMLS_DC)
{
	zval **tmp_zval;
	zend_class_entry *ce;

	if (class == NULL || Z_TYPE_P(class) != IS_OBJECT) {
		return NULL;
	}

	ce = zend_get_class_entry(class TSRMLS_CC);
#if PHP_VERSION_ID >= 50400
	tmp_zval = zend_std_get_static_property(ce, prop_name, strlen(prop_name), 0, NULL TSRMLS_CC);
#else
	tmp_zval = zend_std_get_static_property(ce, prop_name, strlen(prop_name), 0 TSRMLS_CC);
#endif
	return *tmp_zval;
}

zval *TWIG_GET_ARRAY_ELEMENT_ZVAL(zval *class, zval *prop_name TSRMLS_DC)
{
	zval **tmp_zval;
	char *tmp_name;

	if (class == NULL || Z_TYPE_P(class) != IS_ARRAY || Z_TYPE_P(prop_name) != IS_STRING) {
		if (class != NULL && Z_TYPE_P(class) == IS_OBJECT && TWIG_INSTANCE_OF(class, zend_ce_arrayaccess TSRMLS_CC)) {
			// array access object
			return TWIG_GET_ARRAYOBJECT_ELEMENT(class, prop_name TSRMLS_CC);
		}
		return NULL;
	}

	convert_to_string(prop_name);
	tmp_name = Z_STRVAL_P(prop_name);
	if (zend_symtable_find(HASH_OF(class), tmp_name, strlen(tmp_name)+1, (void**) &tmp_zval) == SUCCESS) {
		return *tmp_zval;
	}
	return NULL;
}

zval *TWIG_GET_ARRAY_ELEMENT(zval *class, char *prop_name, int prop_name_length TSRMLS_DC)
{
	zval **tmp_zval;

	if (class == NULL/* || Z_TYPE_P(class) != IS_ARRAY*/) {
		return NULL;
	}

	if (class != NULL && Z_TYPE_P(class) == IS_OBJECT && TWIG_INSTANCE_OF(class, zend_ce_arrayaccess TSRMLS_CC)) {
		// array access object
		zval *tmp_name_zval;
		zval *tmp_ret_zval;

		ALLOC_INIT_ZVAL(tmp_name_zval);
		ZVAL_STRING(tmp_name_zval, prop_name, 1);
		tmp_ret_zval = TWIG_GET_ARRAYOBJECT_ELEMENT(class, tmp_name_zval TSRMLS_CC);
		zval_dtor(tmp_name_zval);
		efree(tmp_name_zval);
		return tmp_ret_zval;
	}

	if (zend_symtable_find(HASH_OF(class), prop_name, prop_name_length+1, (void**)&tmp_zval) == SUCCESS) {
		return *tmp_zval;
	}
	return NULL;
}

zval *TWIG_PROPERTY(zval *object, zval *propname TSRMLS_DC)
{
	char *prot_name;
	int prot_name_length;
	zval *tmp = NULL;

	tmp = TWIG_GET_ARRAY_ELEMENT(object, Z_STRVAL_P(propname), Z_STRLEN_P(propname) TSRMLS_CC);
	if (tmp) {
		return tmp;
	}

	zend_mangle_property_name(&prot_name, &prot_name_length, "*", 1, Z_STRVAL_P(propname), Z_STRLEN_P(propname), 0);
	tmp = TWIG_GET_ARRAY_ELEMENT(object, prot_name, prot_name_length TSRMLS_CC);
	efree(prot_name);
	if (tmp) {
		return tmp;
	}

	if (Z_OBJ_HT_P(object)->read_property) {
#if PHP_VERSION_ID >= 50400
		tmp = Z_OBJ_HT_P(object)->read_property(object, propname, BP_VAR_IS, NULL TSRMLS_CC);
#else
		tmp = Z_OBJ_HT_P(object)->read_property(object, propname, BP_VAR_IS TSRMLS_CC);
#endif
		if (tmp != EG(uninitialized_zval_ptr)) {
			return tmp;
		} else {
			return NULL;
		}
	}
	return tmp;
}

int TWIG_HAS_PROPERTY(zval *object, zval *propname TSRMLS_DC)
{
	if (Z_OBJ_HT_P(object)->has_property) {
#if PHP_VERSION_ID >= 50400
		return Z_OBJ_HT_P(object)->has_property(object, propname, 0, NULL TSRMLS_CC);
#else
		return Z_OBJ_HT_P(object)->has_property(object, propname, 0 TSRMLS_CC);
#endif
	}
	return 0;
}

zval *TWIG_PROPERTY_CHAR(zval *object, char *propname TSRMLS_DC)
{
	zval *tmp_name_zval, *tmp;

	ALLOC_INIT_ZVAL(tmp_name_zval);
	ZVAL_STRING(tmp_name_zval, propname, 1);
	tmp = TWIG_PROPERTY(object, tmp_name_zval TSRMLS_CC);
	zval_dtor(tmp_name_zval);
	efree(tmp_name_zval);
	return tmp;
}

int TWIG_CALL_B_0(zval *object, char *method)
{
	return 0;
}

zval *TWIG_CALL_S(zval *object, char *method, char *arg0 TSRMLS_DC)
{
	zend_fcall_info fci;
	zval **args[1];
	zval *argument;
	zval *zfunction;
	zval *retval_ptr;

	MAKE_STD_ZVAL(argument);
	ZVAL_STRING(argument, arg0, 1);
	args[0] = &argument;

	MAKE_STD_ZVAL(zfunction);
	ZVAL_STRING(zfunction, method, 1);
	fci.size = sizeof(fci);
	fci.function_table = EG(function_table);
	fci.function_name = zfunction;
	fci.symbol_table = NULL;
#if PHP_VERSION_ID >= 50300
	fci.object_ptr = object;
#else
	fci.object_pp = &object;
#endif
	fci.retval_ptr_ptr = &retval_ptr;
	fci.param_count = 1;
	fci.params = args;
	fci.no_separation = 0;

	if (zend_call_function(&fci, NULL TSRMLS_CC) == FAILURE) {
		zval_dtor(argument);
		return 0;
	}
	zval_dtor(zfunction);
	efree(zfunction);
	zval_dtor(argument);
	efree(argument);
	return retval_ptr;
}

int TWIG_CALL_SB(zval *object, char *method, char *arg0 TSRMLS_DC)
{
	zval *retval_ptr;
	int success;

	retval_ptr = TWIG_CALL_S(object, method, arg0 TSRMLS_CC);
	success = (retval_ptr && (Z_TYPE_P(retval_ptr) == IS_BOOL) && Z_LVAL_P(retval_ptr));

	if (retval_ptr) {
		zval_ptr_dtor(&retval_ptr);
	}

	return success;
}

int TWIG_CALL_Z(zval *object, char *method, zval *arg1 TSRMLS_DC)
{
	zend_fcall_info fci;
	zval **args[1];
	zval *zfunction;
	zval *retval_ptr;
	int   success;

	args[0] = &arg1;

	MAKE_STD_ZVAL(zfunction);
	ZVAL_STRING(zfunction, method, 1);
	fci.size = sizeof(fci);
	fci.function_table = EG(function_table);
	fci.function_name = zfunction;
	fci.symbol_table = NULL;
#if PHP_VERSION_ID >= 50300
	fci.object_ptr = object;
#else
	fci.object_pp = &object;
#endif
	fci.retval_ptr_ptr = &retval_ptr;
	fci.param_count = 1;
	fci.params = args;
	fci.no_separation = 0;

	if (zend_call_function(&fci, NULL TSRMLS_CC) == FAILURE) {
		zval_dtor(zfunction);
		efree(zfunction);
		if (retval_ptr) {
			zval_ptr_dtor(&retval_ptr);
		}
		return 0;
	}

	zval_dtor(zfunction);
	efree(zfunction);

	success = (retval_ptr && (Z_TYPE_P(retval_ptr) == IS_BOOL) && Z_LVAL_P(retval_ptr));
	if (retval_ptr) {
		zval_ptr_dtor(&retval_ptr);
	}

	return success;
}

int TWIG_CALL_ZZ(zval *object, char *method, zval *arg1, zval *arg2 TSRMLS_DC)
{
	zend_fcall_info fci;
	zval **args[2];
	zval *zfunction;
	zval *retval_ptr;
	int   success;

	args[0] = &arg1;
	args[1] = &arg2;

	MAKE_STD_ZVAL(zfunction);
	ZVAL_STRING(zfunction, method, 1);
	fci.size = sizeof(fci);
	fci.function_table = EG(function_table);
	fci.function_name = zfunction;
	fci.symbol_table = NULL;
#if PHP_VERSION_ID >= 50300
	fci.object_ptr = object;
#else
	fci.object_pp = &object;
#endif
	fci.retval_ptr_ptr = &retval_ptr;
	fci.param_count = 2;
	fci.params = args;
	fci.no_separation = 0;

	if (zend_call_function(&fci, NULL TSRMLS_CC) == FAILURE) {
		zval_dtor(zfunction);
		return 0;
	}

	zval_dtor(zfunction);

	success = (retval_ptr && (Z_TYPE_P(retval_ptr) == IS_BOOL) && Z_LVAL_P(retval_ptr));
	if (retval_ptr) {
		zval_ptr_dtor(&retval_ptr);
	}

	return success;
}

#ifndef Z_SET_REFCOUNT_P
# define Z_SET_REFCOUNT_P(pz, rc)  pz->refcount = rc
# define Z_UNSET_ISREF_P(pz) pz->is_ref = 0
#endif

void TWIG_NEW(zval *object, char *class, zval *arg0, zval *arg1 TSRMLS_DC)
{
	zend_class_entry **pce;

	if (zend_lookup_class(class, strlen(class), &pce TSRMLS_CC) == FAILURE) {
		return;
	}

	Z_TYPE_P(object) = IS_OBJECT;
	object_init_ex(object, *pce);
	Z_SET_REFCOUNT_P(object, 1);
	Z_UNSET_ISREF_P(object);

	TWIG_CALL_ZZ(object, "__construct", arg0, arg1 TSRMLS_CC);
}

static int twig_add_array_key_to_string(void *pDest TSRMLS_DC, int num_args, va_list args, zend_hash_key *hash_key)
{
	smart_str *buf;
	char *joiner;

	buf = va_arg(args, smart_str*);
	joiner = va_arg(args, char*);

	if (buf->len != 0) {
		smart_str_appends(buf, joiner);
	}

	if (hash_key->nKeyLength == 0) {
		smart_str_append_long(buf, (long) hash_key->h);
	} else {
		char *key, *tmp_str;
		int key_len, tmp_len;
		key = php_addcslashes(hash_key->arKey, hash_key->nKeyLength - 1, &key_len, 0, "'\\", 2 TSRMLS_CC);
		tmp_str = php_str_to_str_ex(key, key_len, "\0", 1, "' . \"\\0\" . '", 12, &tmp_len, 0, NULL);

		smart_str_appendl(buf, tmp_str, tmp_len);
		efree(key);
		efree(tmp_str);
	}

	return 0;
}

char *TWIG_IMPLODE_ARRAY_KEYS(char *joiner, zval *array TSRMLS_DC)
{
	smart_str collector = { 0, 0, 0 };

	smart_str_appendl(&collector, "", 0);
	zend_hash_apply_with_arguments(HASH_OF(array) TSRMLS_CC, twig_add_array_key_to_string, 2, &collector, joiner);
	smart_str_0(&collector);

	return collector.c;
}

static void TWIG_THROW_EXCEPTION(char *exception_name TSRMLS_DC, char *message, ...)
{
	char *buffer;
	va_list args;
	zend_class_entry **pce;

	if (zend_lookup_class(exception_name, strlen(exception_name), &pce TSRMLS_CC) == FAILURE)
	{
		return;
	}

	va_start(args, message);
	vspprintf(&buffer, 0, message, args);
	va_end(args);

	zend_throw_exception_ex(*pce, 0 TSRMLS_CC, buffer);
}

char *TWIG_GET_CLASS_NAME(zval *object TSRMLS_DC)
{
	char *class_name;
	zend_uint class_name_len;

	if (Z_TYPE_P(object) != IS_OBJECT) {
		return "";
	}
	zend_get_object_classname(object, &class_name, &class_name_len TSRMLS_CC);
	return class_name;
}

static int twig_add_method_to_class(void *pDest TSRMLS_DC, int num_args, va_list args, zend_hash_key *hash_key)
{
	zval *retval;
	char *item;
	size_t item_len;
	zend_function *mptr = (zend_function *) pDest;

	if (!(mptr->common.fn_flags & ZEND_ACC_PUBLIC)) {
		return 0;
	}

	retval = va_arg(args, zval*);

	item_len = strlen(mptr->common.function_name);
	item = estrndup(mptr->common.function_name, item_len);
	php_strtolower(item, item_len);

	add_assoc_stringl_ex(retval, item, item_len+1, item, item_len, 0);

	return 0;
}

static int twig_add_property_to_class(void *pDest TSRMLS_DC, int num_args, va_list args, zend_hash_key *hash_key)
{
	zend_class_entry *ce;
	zval *retval;
	char *class_name, *prop_name;
	zend_property_info *pptr = (zend_property_info *) pDest;

	if (!(pptr->flags & ZEND_ACC_PUBLIC)) {
		return 0;
	}

	ce = *va_arg(args, zend_class_entry**);
	retval = va_arg(args, zval*);

	zend_unmangle_property_name(pptr->name, pptr->name_length, &class_name, &prop_name);

	add_assoc_string(retval, prop_name, prop_name, 1);

	return 0;
}

/* {{{ _adddynproperty */
static int twig_add_dyn_property_to_class(void *pDest TSRMLS_DC, int num_args, va_list args, zend_hash_key *hash_key)
{
	zend_class_entry *ce = *va_arg(args, zend_class_entry**);
	zval *retval = va_arg(args, zval*), member;
	char *class_name, *prop_name;

	if (hash_key->nKeyLength < 1 || hash_key->arKey[0] == '\0') {
		return 0; /* non public cannot be dynamic */
	}

	ZVAL_STRINGL(&member, hash_key->arKey, hash_key->nKeyLength-1, 0);
	if (zend_get_property_info(ce, &member, 1 TSRMLS_CC) == &EG(std_property_info)) {
		zend_unmangle_property_name((&EG(std_property_info))->name, (&EG(std_property_info))->name_length, &class_name, &prop_name);
		add_assoc_string(retval, prop_name, prop_name, 1);
	}
	return 0;
}

static void twig_add_class_to_cache(zval *cache, zval *object, char *class_name TSRMLS_DC)
{
	zval *class_info, *class_methods, *class_properties;
	zend_class_entry *class_ce;

	class_ce = zend_get_class_entry(object TSRMLS_CC);

	ALLOC_INIT_ZVAL(class_info);
	ALLOC_INIT_ZVAL(class_methods);
	ALLOC_INIT_ZVAL(class_properties);
	array_init(class_info);
	array_init(class_methods);
	array_init(class_properties);
	// add all methods to self::cache[$class]['methods']
	zend_hash_apply_with_arguments(&class_ce->function_table TSRMLS_CC, twig_add_method_to_class, 1, class_methods);
	zend_hash_apply_with_arguments(&class_ce->properties_info TSRMLS_CC, twig_add_property_to_class, 2, &class_ce, class_properties);

	if (object && Z_OBJ_HT_P(object)->get_properties) {
		HashTable *properties = Z_OBJ_HT_P(object)->get_properties(object TSRMLS_CC);
		zend_hash_apply_with_arguments(properties TSRMLS_CC, twig_add_dyn_property_to_class, 2, &class_ce, class_properties);
	}
	add_assoc_zval(class_info, "methods", class_methods);
	add_assoc_zval(class_info, "properties", class_properties);
	add_assoc_zval(cache, class_name, class_info);
}

/* {{{ proto mixed twig_template_get_attributes(TwigTemplate template, mixed object, mixed item, array arguments, string type, boolean isDefinedTest, boolean ignoreStrictCheck)
   A C implementation of TwigTemplate::getAttribute() */
PHP_FUNCTION(twig_template_get_attributes)
{
	zval *template;
	zval *object;
	char *item;
	int  item_len;
	zval  zitem;
	zval *arguments = NULL;
	zval *ret = NULL;
	char *type = NULL;
	int   type_len = 0;
	zend_bool isDefinedTest = 0;
	zend_bool ignoreStrictCheck = 0;
	int free_ret = 0;
	zval *tmp_self_cache;


	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ozs|asbb", &template, &object, &item, &item_len, &arguments, &type, &type_len, &isDefinedTest, &ignoreStrictCheck) == FAILURE) {
		return;
	}

	INIT_PZVAL(&zitem);
	ZVAL_STRINGL(&zitem, item, item_len, 0);

	if (!type) {
		type = "any";
	}

/*
	// array
	if (Twig_TemplateInterface::METHOD_CALL !== $type) {
		if ((is_array($object) && array_key_exists($item, $object))
			|| ($object instanceof ArrayAccess && isset($object[$item]))
		) {
			if ($isDefinedTest) {
				return true;
			}

			return $object[$item];
		}
*/
	if (strcmp("method", type) != 0) {
//		printf("XXXmethod: %s\n", type);
		if ((TWIG_ARRAY_KEY_EXISTS(object, item, item_len))
			|| (TWIG_INSTANCE_OF(object, zend_ce_arrayaccess TSRMLS_CC) && TWIG_ISSET_ARRAYOBJECT_ELEMENT(object, &zitem TSRMLS_CC))
		) {
			zval *ret;

			if (isDefinedTest) {
				RETURN_TRUE;
			}

			ret = TWIG_GET_ARRAY_ELEMENT(object, item, item_len TSRMLS_CC);
			RETVAL_ZVAL(ret, 1, 0);
			if (free_ret) {
				zval_ptr_dtor(&ret);
			}
			return;
		}
/*
		if (Twig_TemplateInterface::ARRAY_CALL === $type) {
			if ($isDefinedTest) {
				return false;
			}
			if ($ignoreStrictCheck || !$this->env->isStrictVariables()) {
				return null;
			}
*/
		if (strcmp("array", type) == 0) {
			if (isDefinedTest) {
				RETURN_FALSE;
			}
			if (ignoreStrictCheck || !TWIG_CALL_BOOLEAN(TWIG_PROPERTY_CHAR(template, "env" TSRMLS_CC), "isStrictVariables" TSRMLS_CC)) {
				return;
			}
/*
			if (is_object($object)) {
				throw new Twig_Error_Runtime(sprintf('Key "%s" in object (with ArrayAccess) of type "%s" does not exist', $item, get_class($object)));
			// array
			} else {
				throw new Twig_Error_Runtime(sprintf('Key "%s" for array with keys "%s" does not exist', $item, implode(', ', array_keys($object))));
			}
		}
	}
*/
			if (Z_TYPE_P(object) == IS_OBJECT) {
				TWIG_THROW_EXCEPTION("Twig_Error_Runtime" TSRMLS_CC, "Key \"%s\" in object (with ArrayAccess) of type \"%s\" does not exist", item, TWIG_GET_CLASS_NAME(object TSRMLS_CC));
			} else {
				TWIG_THROW_EXCEPTION("Twig_Error_Runtime" TSRMLS_CC, "Key \"%s\" for array with keys \"%s\" does not exist", item, TWIG_IMPLODE_ARRAY_KEYS(", ", object TSRMLS_CC));
			}
			return;
		}
	}

/*
	if (!is_object($object)) {
		if ($isDefinedTest) {
			return false;
		}
*/

	if (Z_TYPE_P(object) != IS_OBJECT) {
		if (isDefinedTest) {
			RETURN_FALSE;
		}
/*
		if ($ignoreStrictCheck || !$this->env->isStrictVariables()) {
			return null;
		}
		throw new Twig_Error_Runtime(sprintf('Item "%s" for "%s" does not exist', $item, implode(', ', array_keys($object))));
	}
*/
		if (ignoreStrictCheck || !TWIG_CALL_BOOLEAN(TWIG_PROPERTY_CHAR(template, "env" TSRMLS_CC), "isStrictVariables" TSRMLS_CC)) {
			RETURN_FALSE;
		}
		if (Z_TYPE_P(object) == IS_ARRAY) {
			TWIG_THROW_EXCEPTION("Twig_Error_Runtime" TSRMLS_CC, "Item \"%s\" for \"Array\" does not exist", item);
		} else {
			Z_ADDREF_P(object);
			convert_to_string_ex(&object);
			TWIG_THROW_EXCEPTION("Twig_Error_Runtime" TSRMLS_CC, "Item \"%s\" for \"%s\" does not exist", item, Z_STRVAL_P(object));
			zval_ptr_dtor(&object);
		}
		return;
	}
/*
	// get some information about the object
	$class = get_class($object);
	if (!isset(self::$cache[$class])) {
		$r = new ReflectionClass($class);
		self::$cache[$class] = array('methods' => array(), 'properties' => array());
		foreach ($r->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
			self::$cache[$class]['methods'][strtolower($method->getName())] = true;
		}

		foreach ($r->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
			self::$cache[$class]['properties'][$property->getName()] = true;
		}
	}
*/
	if (Z_TYPE_P(object) == IS_OBJECT) {
		char *class_name = NULL;

		class_name = TWIG_GET_CLASS_NAME(object TSRMLS_CC);
		tmp_self_cache = TWIG_GET_STATIC_PROPERTY(template, "cache" TSRMLS_CC);

		if (!TWIG_GET_ARRAY_ELEMENT(tmp_self_cache, class_name, strlen(class_name) TSRMLS_CC)) {
			twig_add_class_to_cache(tmp_self_cache, object, class_name TSRMLS_CC);
		}
		efree(class_name);
	}

/*
	// object property
	if (Twig_TemplateInterface::METHOD_CALL !== $type) {
		if (isset(self::$cache[$class]['properties'][$item])
			|| isset($object->$item) || array_key_exists($item, $object)
		) {
			if ($isDefinedTest) {
				return true;
			}
			if ($this->env->hasExtension('sandbox')) {
				$this->env->getExtension('sandbox')->checkPropertyAllowed($object, $item);
			}

			return $object->$item;
		}
	}
*/
	if (strcmp("method", type) != 0) {
		zval *tmp_class, *tmp_properties, *tmp_item;
		char *class_name = NULL;

		class_name = TWIG_GET_CLASS_NAME(object TSRMLS_CC);
		tmp_class = TWIG_GET_ARRAY_ELEMENT(tmp_self_cache, class_name, strlen(class_name) TSRMLS_CC);
		tmp_properties = TWIG_GET_ARRAY_ELEMENT(tmp_class, "properties", strlen("properties") TSRMLS_CC);
		tmp_item = TWIG_GET_ARRAY_ELEMENT(tmp_properties, item, item_len TSRMLS_CC);

		efree(class_name);

		if (tmp_item || TWIG_HAS_PROPERTY(object, &zitem TSRMLS_CC) || TWIG_ARRAY_KEY_EXISTS(object, item, item_len) // FIXME: Array key? is that array access here?
		) {
			if (isDefinedTest) {
				RETURN_TRUE;
			}
			if (TWIG_CALL_SB(TWIG_PROPERTY_CHAR(template, "env" TSRMLS_CC), "hasExtension", "sandbox" TSRMLS_CC)) {
				TWIG_CALL_ZZ(TWIG_CALL_S(TWIG_PROPERTY_CHAR(template, "env" TSRMLS_CC), "getExtension", "sandbox" TSRMLS_CC), "checkPropertyAllowed", object, &zitem TSRMLS_CC);
			}
			if (EG(exception)) {
				return;
			}

			ret = TWIG_PROPERTY(object, &zitem TSRMLS_CC);
			RETURN_ZVAL(ret, 1, 0);
		}
	}
/*
	// object method
	$lcItem = strtolower($item);
	if (isset(self::$cache[$class]['methods'][$lcItem])) {
		$method = $item;
	} elseif (isset(self::$cache[$class]['methods']['get'.$lcItem])) {
		$method = 'get'.$item;
	} elseif (isset(self::$cache[$class]['methods']['is'.$lcItem])) {
		$method = 'is'.$item;
	} elseif (isset(self::$cache[$class]['methods']['__call'])) {
		$method = $item;
*/
	{
		char *lcItem = TWIG_STRTOLOWER(item, item_len);
		int   lcItem_length;
		char *method = NULL;
		char *tmp_method_name_get;
		char *tmp_method_name_is;
		zval *tmp_class, *tmp_methods;
		char *class_name = NULL;

		class_name = TWIG_GET_CLASS_NAME(object TSRMLS_CC);
		lcItem_length = strlen(lcItem);
		tmp_method_name_get = emalloc(4 + lcItem_length);
		tmp_method_name_is  = emalloc(3 + lcItem_length);

		sprintf(tmp_method_name_get, "get%s", lcItem);
		sprintf(tmp_method_name_is, "is%s", lcItem);

		tmp_class = TWIG_GET_ARRAY_ELEMENT(tmp_self_cache, class_name, strlen(class_name) TSRMLS_CC);
		tmp_methods = TWIG_GET_ARRAY_ELEMENT(tmp_class, "methods", strlen("methods") TSRMLS_CC);
		efree(class_name);

		if (TWIG_GET_ARRAY_ELEMENT(tmp_methods, lcItem, lcItem_length TSRMLS_CC)) {
			method = item;
		} else if (TWIG_GET_ARRAY_ELEMENT(tmp_methods, tmp_method_name_get, lcItem_length + 3 TSRMLS_CC)) {
			method = tmp_method_name_get;
		} else if (TWIG_GET_ARRAY_ELEMENT(tmp_methods, tmp_method_name_is, lcItem_length + 2 TSRMLS_CC)) {
			method = tmp_method_name_is;
		} else if (TWIG_GET_ARRAY_ELEMENT(tmp_methods, "__call", 6 TSRMLS_CC)) {
			method = item;
/*
	} else {
		if ($isDefinedTest) {
			return false;
		}
		if ($ignoreStrictCheck || !$this->env->isStrictVariables()) {
			return null;
		}
		throw new Twig_Error_Runtime(sprintf('Method "%s" for object "%s" does not exist', $item, get_class($object)));
	}
	if ($isDefinedTest) {
		return true;
	}
*/
		} else {
			if (isDefinedTest) {
				RETURN_FALSE;
			}
			if (ignoreStrictCheck || !TWIG_CALL_BOOLEAN(TWIG_PROPERTY_CHAR(template, "env" TSRMLS_CC), "isStrictVariables" TSRMLS_CC)) {
				return;
			}
			TWIG_THROW_EXCEPTION("Twig_Error_Runtime" TSRMLS_CC, "Method \"%s\" for object \"%s\" does not exist", item, TWIG_GET_CLASS_NAME(object TSRMLS_CC));
			return;
		}
		if (isDefinedTest) {
			efree(tmp_method_name_get);
			efree(tmp_method_name_is);
			efree(lcItem);
			RETURN_TRUE;
		}
/*
	if ($this->env->hasExtension('sandbox')) {
		$this->env->getExtension('sandbox')->checkMethodAllowed($object, $method);
	}
*/
		if (TWIG_CALL_SB(TWIG_PROPERTY_CHAR(template, "env" TSRMLS_CC), "hasExtension", "sandbox" TSRMLS_CC)) {
			TWIG_CALL_ZZ(TWIG_CALL_S(TWIG_PROPERTY_CHAR(template, "env" TSRMLS_CC), "getExtension", "sandbox" TSRMLS_CC), "checkMethodAllowed", object, &zitem TSRMLS_CC);
		}
		if (EG(exception)) {
			return;
		}
/*
	$ret = call_user_func_array(array($object, $method), $arguments);
*/
		if (Z_TYPE_P(object) == IS_OBJECT) {
			ret = TWIG_CALL_USER_FUNC_ARRAY(object, method, arguments TSRMLS_CC);
			free_ret = 1;
		}
		efree(tmp_method_name_get);
		efree(tmp_method_name_is);
		efree(lcItem);
	}
/*
	if ($object instanceof Twig_TemplateInterface) {
		return new Twig_Markup($ret, $this->env->getCharset());
	}
*/
	if (TWIG_INSTANCE_OF_USERLAND(object, "Twig_TemplateInterface" TSRMLS_CC)) {
		zval *charset = TWIG_CALL_USER_FUNC_ARRAY(TWIG_PROPERTY_CHAR(template, "env" TSRMLS_CC), "getCharset", NULL TSRMLS_CC);
		TWIG_NEW(return_value, "Twig_Markup", ret, charset TSRMLS_CC);
		zval_ptr_dtor(&charset);
		if (ret) {
			zval_ptr_dtor(&ret);
		}
		return;
	}
/*
	return $ret;
*/
	if (ret) {
		RETVAL_ZVAL(ret, 1, 0);
		if (free_ret) {
			zval_ptr_dtor(&ret);
		}
	}
}
