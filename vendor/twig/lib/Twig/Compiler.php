<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Compiles a node to PHP code.
 *
 * @package    twig
 * @author     Fabien Potencier <fabien@symfony.com>
 */
class Twig_Compiler implements Twig_CompilerInterface
{
    protected $lastLine;
    protected $source;
    protected $indentation;
    protected $env;
    protected $debugInfo;
    protected $sourceOffset;
    protected $sourceLine;

    /**
     * Constructor.
     *
     * @param Twig_Environment $env The twig environment instance
     */
    public function __construct(Twig_Environment $env)
    {
        $this->env = $env;
        $this->debugInfo = array();
    }

    /**
     * Returns the environment instance related to this compiler.
     *
     * @return Twig_Environment The environment instance
     */
    public function getEnvironment()
    {
        return $this->env;
    }

    /**
     * Gets the current PHP code after compilation.
     *
     * @return string The PHP code
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Compiles a node.
     *
     * @param Twig_NodeInterface $node        The node to compile
     * @param integer            $indentation The current indentation
     *
     * @return Twig_Compiler The current compiler instance
     */
    public function compile(Twig_NodeInterface $node, $indentation = 0)
    {
        $this->lastLine = null;
        $this->source = '';
        $this->sourceOffset = 0;
        $this->sourceLine = 0;
        $this->indentation = $indentation;

        $node->compile($this);

        return $this;
    }

    public function subcompile(Twig_NodeInterface $node, $raw = true)
    {
        if (false === $raw) {
            $this->addIndentation();
        }

        $node->compile($this);

        return $this;
    }

    /**
     * Adds a raw string to the compiled code.
     *
     * @param  string $string The string
     *
     * @return Twig_Compiler The current compiler instance
     */
    public function raw($string)
    {
        $this->source .= $string;

        return $this;
    }

    /**
     * Writes a string to the compiled code by adding indentation.
     *
     * @return Twig_Compiler The current compiler instance
     */
    public function write()
    {
        $strings = func_get_args();
        foreach ($strings as $string) {
            $this->addIndentation();
            $this->source .= $string;
        }

        return $this;
    }

    public function addIndentation()
    {
        $this->source .= str_repeat(' ', $this->indentation * 4);

        return $this;
    }

    /**
     * Adds a quoted string to the compiled code.
     *
     * @param  string $value The string
     *
     * @return Twig_Compiler The current compiler instance
     */
    public function string($value)
    {
        $this->source .= sprintf('"%s"', addcslashes($value, "\0\t\"\$\\"));

        return $this;
    }

    /**
     * Returns a PHP representation of a given value.
     *
     * @param  mixed $value The value to convert
     *
     * @return Twig_Compiler The current compiler instance
     */
    public function repr($value)
    {
        if (is_int($value) || is_float($value)) {
            if (false !== $locale = setlocale(LC_NUMERIC, 0)) {
                setlocale(LC_NUMERIC, 'C');
            }

            $this->raw($value);

            if (false !== $locale) {
                setlocale(LC_NUMERIC, $locale);
            }
        } elseif (null === $value) {
            $this->raw('null');
        } elseif (is_bool($value)) {
            $this->raw($value ? 'true' : 'false');
        } elseif (is_array($value)) {
            $this->raw('array(');
            $i = 0;
            foreach ($value as $key => $value) {
                if ($i++) {
                    $this->raw(', ');
                }
                $this->repr($key);
                $this->raw(' => ');
                $this->repr($value);
            }
            $this->raw(')');
        } else {
            $this->string($value);
        }

        return $this;
    }

    /**
     * Adds debugging information.
     *
     * @param Twig_NodeInterface $node The related twig node
     *
     * @return Twig_Compiler The current compiler instance
     */
    public function addDebugInfo(Twig_NodeInterface $node)
    {
        if ($node->getLine() != $this->lastLine) {
            $this->sourceLine += substr_count($this->source, "\n", $this->sourceOffset);
            $this->sourceOffset = strlen($this->source);
            $this->debugInfo[$this->sourceLine] = $node->getLine();

            $this->lastLine = $node->getLine();
            $this->write("// line {$node->getLine()}\n");
        }

        return $this;
    }

    public function getDebugInfo()
    {
        return $this->debugInfo;
    }

    /**
     * Indents the generated code.
     *
     * @param integer $step The number of indentation to add
     *
     * @return Twig_Compiler The current compiler instance
     */
    public function indent($step = 1)
    {
        $this->indentation += $step;

        return $this;
    }

    /**
     * Outdents the generated code.
     *
     * @param integer $step The number of indentation to remove
     *
     * @return Twig_Compiler The current compiler instance
     */
    public function outdent($step = 1)
    {
        $this->indentation -= $step;

        if ($this->indentation < 0) {
            throw new Twig_Error('Unable to call outdent() as the indentation would become negative');
        }

        return $this;
    }
}
