<?php

namespace solver\solverBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use solver\solverBundle\Form\entitesType;
use solver\solverBundle\Form\problemesType;
use solver\solverBundle\Form\solutionsType;

/**
 * @Route("/Admin")
 */
class AdminController extends Controller {

    /**
     * @Route("/", name="admin_home")
     * @Template()
     */
    public function indexAction() {
        return $this->render('solversolverBundle:Admin:layout.html.twig');
    }

//*************************************************************************************************************

    /**
     * @Route("/Ajax/Entites", name="admin_ajax_entites")
     */
    public function admin_ajax_entitesAction() {
        $em = $this->getDoctrine()->getEntityManager();

        $entites = $em->getRepository('solversolverBundle:entites')->findBy(array(), array('libelle' => 'ASC'));
        $nouvelle_entite = new \solver\solverBundle\Entity\entites();
        $form_nouvelle_entite = $this->createForm(new entitesType(), $nouvelle_entite);

        for ($i = 0; $i < count($entites); $i++) {
            $forms[$i] = $this->createForm(new entitesType(), $entites[$i])->createView();
        }

        return $this->render('solversolverBundle:Admin/Ajax:entites.html.twig', array('entites' => $entites,
                    'forms' => $forms,
                    'nouvelle_entite' => $nouvelle_entite,
                    'form_nouvelle_entite' => $form_nouvelle_entite->createView(),
                ));
    }

    /**
     * @Route("/Ajax/Entite/{id}/update", name="admin_ajax_entite_update")
     * @Method("post")
     */
    public function admin_ajax_entitesUpdateAction($id) {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('solversolverBundle:entites')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entites entity.');
        }

        $editForm = $this->createForm(new entitesType(), $entity);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return new response('');
        } else {
            throw $this->createNotFoundException('Erreur de validation');
        }
    }

    /**
     * @Route("/Entite/Create", name="admin_entite_create")
     * @Method("post")
     */
    public function admin_entite_createAction() {
        $entite = new \solver\solverBundle\Entity\entites();
        $request = $this->getRequest();
        $form = $this->createForm(new entitesType(), $entite);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entite);
            $em->flush();

            $this->get('session')->setFlash('success', 'L\'entité a été correctement ajoutée.');
            //return $this->redirect($this->generateUrl('problemes_show', array('id' => $probleme->getId())));
            return $this->redirect($this->generateUrl('admin_home'));
        }

        throw new \Exception('Erreur de validation');
    }

//*************************************************************************************************************

    /**
     * @Route("/Ajax/Problemes", name="admin_ajax_problemes")
     */
    public function admin_ajax_problemesAction() {
        $em = $this->getDoctrine()->getEntityManager();

        $problemes = $em->getRepository('solversolverBundle:problemes')->findBy(array(), array('libelle' => 'ASC'));
        $nouveau_probleme = new \solver\solverBundle\Entity\problemes();
        $form_nouveau_probleme = $this->createForm(new problemesType(), $nouveau_probleme);

        for ($i = 0; $i < count($problemes); $i++) {
            $forms[$i] = $this->createForm(new problemesType(), $problemes[$i])->createView();
        }

        return $this->render('solversolverBundle:Admin/Ajax:problemes.html.twig', array('problemes' => $problemes,
                    'forms' => $forms,
                    'nouveau_probleme' => $nouveau_probleme,
                    'form_nouveau_probleme' => $form_nouveau_probleme->createView(),
                ));
    }

    /**
     * @Route("/Ajax/Probleme/{id}/update", name="admin_ajax_probleme_update")
     * @Method("post")
     */
    public function admin_ajax_problemesUpdateAction($id) {
        $em = $this->getDoctrine()->getEntityManager();

        $problemes = $em->getRepository('solversolverBundle:problemes')->find($id);

        if (!$problemes) {
            throw $this->createNotFoundException('Unable to find problemes entity.');
        }

        $editForm = $this->createForm(new problemesType(), $problemes);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($problemes);
            $em->flush();

            return new response('');
        } else {
            throw $this->createNotFoundException('Erreur de validation');
        }
    }

    /**
     * @Route("/Probleme/Create", name="admin_probleme_create")
     * @Method("post")
     */
    public function admin_probleme_createAction() {
        $probleme = new \solver\solverBundle\Entity\problemes();
        $request = $this->getRequest();
        $form = $this->createForm(new problemesType(), $probleme);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($probleme);
            $em->flush();

            $this->get('session')->setFlash('success', 'Le problème a été correctement ajouté.');
            //return $this->redirect($this->generateUrl('problemes_show', array('id' => $probleme->getId())));
            return $this->redirect($this->generateUrl('admin_home'));
        }

        throw new \Exception('Erreur de validation');
    }

//*************************************************************************************************************

    /**
     * @Route("/Ajax/Solutions", name="admin_ajax_solutions")
     */
    public function admin_ajax_solutionsAction() {
        $em = $this->getDoctrine()->getEntityManager();

        $solutions = $em->getRepository('solversolverBundle:solutions')->findBy(array(), array('libelle' => 'ASC'));
        $nouvelle_solution = new \solver\solverBundle\Entity\solutions();
        $form_nouvelle_solution = $this->createForm(new solutionsType(), $nouvelle_solution);

        for ($i = 0; $i < count($solutions); $i++) {
            $forms[$i] = $this->createForm(new solutionsType(), $solutions[$i])->createView();
        }

        return $this->render('solversolverBundle:Admin/Ajax:solutions.html.twig', array('solutions' => $solutions,
                    'forms' => $forms,
                    'nouvelle_solution' => $nouvelle_solution,
                    'form_nouvelle_solution' => $form_nouvelle_solution->createView(),
                ));
    }

    /**
     * @Route("/Ajax/Solution/{id}/update", name="admin_ajax_solution_update")
     * @Method("post")
     */
    public function admin_ajax_solutionUpdateAction($id) {
        $em = $this->getDoctrine()->getEntityManager();

        $solution = $em->getRepository('solversolverBundle:solutions')->find($id);

        if (!$solution) {
            throw $this->createNotFoundException('Unable to find solutions entity.');
        }

        $editForm = $this->createForm(new solutionsType(), $solution);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($solution);
            $em->flush();

            return new response('');
        } else {
            throw $this->createNotFoundException('Erreur de validation');
        }
    }

    /**
     * @Route("/Solution/Create", name="admin_solution_create")
     * @Method("post")
     */
    public function admin_solution_createAction() {
        $solution = new \solver\solverBundle\Entity\solutions();
        $request = $this->getRequest();
        $form = $this->createForm(new solutionsType(), $solution);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($solution);
            $em->flush();

            $this->get('session')->setFlash('success', 'La solution a été correctement ajouté.');
            return $this->redirect($this->generateUrl('admin_home'));
        }

        throw new \Exception('Erreur de validation');
    }

//*************************************************************************************************************

    /**
     * Action permettant d'afficher le template twig du treeview.
     *
     * @Route("/Ajax/Liaisons", name="admin_ajax_liaisons")
     */
    public function admin_ajax_liaisonsAction() {
        //$em = $this->getDoctrine()->getEntityManager();
        //$entites=$em->getRepository('solversolverBundle:entites')->findBy(array(), array('libelle'=>'ASC'));
        return $this->render('solversolverBundle:Admin/Ajax:liaisons.html.twig');
    }

    /**
     * Action appelée lors de l'affichage du treeview.
     * Elle retourne, selon le noeud appelant, la liste des entités, la liste des problèmes liés avec les solutions, etc...
     * c'est cete action qui gère tout l'affichage du treeview.
     *
     * @Route("/Ajax/Liaisons/Treeview/Load", name="admin_ajax_liaisonsTreeviewLoad")
     */
    public function admin_ajax_liaisonsTreeviewLoadAction() {
        $request = $this->getRequest();
        $params = $request->query->get('id');
        $em = $this->getDoctrine()->getEntityManager();

        if ($params) {
            $chain = explode("&", $params); //arborescence passée en paramètres
            $level = sizeof($chain) - 1;      //niveau d'arborescence passé en paramètres (le niveau 0 est celui des entités)

            if (substr($chain[$level], 0, 1) == 'e') {
                //level 0, on va renvoyer les problèmes liés à l'entité
                $entite_id = substr($chain[$level], 1, strlen($chain[$level]) - 1);
                $problemes = $em->getRepository('solversolverBundle:problemes')->findBy(array('entite' => $entite_id, 'existe' => true), array('libelle' => 'ASC'));
                return $this->render('solversolverBundle:Admin/Ajax/Treeview:problemesParEntite.html.twig', array('problemes' => $problemes,));
            } else {
                //level supérieur à 0, on va renvoyer les problèmes liés à un problème
                $probleme_id = substr($chain[$level], 1, strlen($chain[$level]) - 1);

                //$problemes = $em->getRepository('solversolverBundle:probleme_probleme')->findBy(array('probleme_aval' => $probleme_id), array());
                $problemes = $em->getRepository('solversolverBundle:probleme_probleme')->getProblemesAmont($probleme_id);
                $solutions = $em->getRepository("solversolverBundle:solutions")->findBy(array("probleme" => $probleme_id, 'existe' => true), array("priorite" => "ASC", "titre" => "ASC"));

                return $this->render('solversolverBundle:Admin/Ajax/Treeview:problemesParProbleme.html.twig', array('problemes' => $problemes,
                            'solutions' => $solutions,
                                )
                );
            }
        } else {
            //pas d'id définie, on va donc renvoyer la liste des entités
            $entites = $em->getRepository('solversolverBundle:entites')->findBy(array('existe' => true), array('libelle' => 'ASC'));
            return $this->render('solversolverBundle:Admin/Ajax/Treeview:entites.html.twig', array('entites' => $entites,));
        }
    }

    /**
     * @Route("/Ajax/Liaisons/Treeview/Rename", name="admin_ajax_liaisonstreeview_Rename")
     */
    public function admin_ajax_liaisonsTreeviewRenameAction() {
        $request = $this->getRequest();
        $param_id = $request->request->get('id');
        $param_type = $request->request->get('type');
        $param_value = $request->get('libelle');

        if (!$param_id) {
            throw $this->createNotFoundException('Erreur: paramètre "id" introuvable.');
        }
        if (!$param_value) {
            throw $this->createNotFoundException('Erreur: paramètre "libelle" introuvable.');
        }
        if (!$param_type) {
            throw $this->createNotFoundException('Erreur: paramètre "type" introuvable.');
        }

        $em = $this->getDoctrine()->getEntityManager();

        //if (substr($param_id, 0, 1) == 'e') {
        if ($param_type == 'entite') {
            //l'id commence par "e", c'est donc une entité

            $entite_id = substr($param_id, 1, strlen($param_id) - 1);
            $entite = $em->getRepository('solversolverBundle:entites')->find($entite_id);
            if (!$entite) {
                throw $this->createNotFoundException('Erreur: entité introuvable.');
            }
            $entite->setLibelle($param_value);

            $validator = $this->get('validator');
            $errors = $validator->validate($entite);

            if (count($errors) == 0) {
                $em->persist($entite);
                $em->flush();
                return new Response('ok');
            } else {
                //throw new \Exception('Erreur de validation');
                return new Response(print_r($errors, true));
            }
        } elseif ($param_type == 'probleme') {
            //l'id ne commence pas par e, c'est donc un problème

            $probleme_id = substr($param_id, 1, strlen($param_id) - 1);
            $probleme = $em->getRepository('solversolverBundle:problemes')->find($probleme_id);
            if (!$probleme) {
                throw $this->createNotFoundException('Erreur: probleme introuvable.');
            }
            $probleme->setLibelle($param_value);

            $validator = $this->get('validator');
            $errors = $validator->validate($probleme);
            if (count($errors) == 0) {
                $em->persist($probleme);
                $em->flush();
                return new Response('ok');
            } else {
                //throw new \Exception('Erreur de validation');
                return new Response(print_r($errors, true));
            }
        } elseif ($param_type == 'solution') {
            //on va renommer une solution

            $solution_id = substr($param_id, 1, strlen($param_id) - 1);
            $solution = $em->getRepository('solversolverBundle:solutions')->find($solution_id);
            if (!$solution) {
                throw $this->createNotFoundException('Erreur: solution introuvable.');
            }
            $solution->setTitre($param_value);

            $validator = $this->get('validator');
            $errors = $validator->validate($solution);
            if (count($errors) == 0) {
                $em->persist($solution);
                $em->flush();
                return new Response('ok');
            } else {
                return new Response(print_r($errors, true));
            }
        } else {
            //Type envoyé inconnu, on va renvoyer une erreur
            throw $this->createNotFoundException('Erreur: Type envoyé inconnu, ou non géré par le contrôleur.');
        }
    }

    /**
     * @route("/Ajax/Liaisons/Treeview/Create", name="admin_ajax_liaisonstreeview_create")
     */
    public function admin_ajax_liaisonsTreeviewCreateAction() {
        $request = $this->getRequest();
        $parent_id = $request->request->get('parent_id');
        $libelle = $request->get('libelle');
        $type = $request->get('type');

        if (!$parent_id || !$libelle || !$type) {
            throw new \Exception('Erreur de passage de paramètres');
        }

        $em = $this->getDoctrine()->getEntityManager();


        if ($parent_id == "root") {
            //si le parent est la racine, on va ajouter une entité
            $entite = new \solver\solverBundle\Entity\entites();
            $entite->setLibelle($libelle);
            $validator = $this->get('validator');
            $errors = $validator->validate($entite);
            if (count($errors) == 0) {
                $em->persist($entite);
                $em->flush();

                $response = new Response(json_encode(array("type" => "entite",
                                    "id" => $entite->getId(),
                                    "libelle" => $entite->getLibelle(),
                                    "existe" => $entite->getExiste()
                                        )
                                )
                );
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            } else {
                $response = new Response(json_encode(array("type" => "error",
                                    "libelle" => $errors)));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        } else {
            //si le parent n'est pas la racine, on va ajouter un problème, il  a cependant 2 types de problèmes, un problème lié à une entité, ou un problème lié à un autre problème
            //cela dépends du type de racine parent, si c'est une entité (e) ou un problème (p)
            if (substr($parent_id, 0, 1) == "e") {
                //le noeud parent est une entité
                $probleme = new \solver\solverBundle\Entity\problemes();
                $entite_id = substr($parent_id, 1, strlen($parent_id) - 1);
                $entite = $em->getRepository('solversolverBundle:entites')->find($entite_id);
                if (!$entite) {
                    throw $this->createNotFoundException('Erreur: entité introuvable.');
                }

                $probleme->setLibelle($libelle);
                $probleme->setEntite($entite);

                $validator = $this->get('validator');
                $errors = $validator->validate($probleme);
                if (count($errors) == 0) {
                    $em->persist($probleme);
                    $em->flush();

                    $response = new Response(json_encode(array("type" => "probleme",
                                        "id" => $probleme->getId(),
                                        "libelle" => $probleme->getLibelle(),
                                        "existe" => $probleme->getExiste()
                                            )
                                    )
                    );
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                } else {
                    $response = new Response(json_encode(array("type" => "error",
                                        "libelle" => $errors)));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
            } elseif (substr($parent_id, 0, 1) == "p") {
                //le noeud parent est un problème, on peut ajouter soit un problème, soit une solution, cela va dépendre du paramètre "type" envoyé
                if ($type == "probleme") {
                    //on ajoute un problème sur un problème
                    //il faut donc ajouter le problème, puis sa liaison dans la table probleme_probleme avec le problème parent
                    $probleme = new \solver\solverBundle\Entity\problemes();
                    $probleme_parent_id = substr($parent_id, 1, strlen($parent_id) - 1);
                    $probleme_parent = $em->getRepository('solversolverBundle:problemes')->find($probleme_parent_id);
                    if (!$probleme_parent) {
                        throw $this->createNotFoundException('Erreur: problème parent introuvable.');
                    }
                    $liaison_problemes = new \solver\solverBundle\Entity\probleme_probleme();

                    $probleme->setLibelle($libelle);
                    $liaison_problemes->setProblemeAval($probleme_parent);
                    $liaison_problemes->setProblemeAmont($probleme);
                    //TODO: validation de la liaison entre problèmes

                    $validator = $this->get('validator');
                    $errors = $validator->validate($probleme);
                    if (count($errors) == 0) {
                        $em->persist($probleme);
                        $em->flush();
                        //$em->persist($probleme_parent);
                        $em->persist($liaison_problemes);
                        $em->flush();

                        $response = new Response(json_encode(array("type" => "probleme",
                                            "id" => $probleme->getId(),
                                            "libelle" => $probleme->getLibelle(),
                                            "existe" => $probleme->getExiste()
                                                )
                                        )
                        );
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    } else {
                        $response = new Response(json_encode(array("type" => "error",
                                            "libelle" => $errors)));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                } elseif ($type == "solution") {
                    //on ajoute une solution à un problème
                    $solution = new \solver\solverBundle\Entity\solutions();
                    $probleme_id = substr($parent_id, 1, strlen($parent_id) - 1);
                    $probleme = $em->getRepository('solversolverBundle:problemes')->find($probleme_id);
                    if (!$probleme) {
                        throw $this->createNotFoundException('Erreur: problème parent introuvable.');
                    }
                    $solution->setTitre($libelle);
                    $solution->setProbleme($probleme);

                    $validator = $this->get('validator');
                    $errors = $validator->validate($solution);
                    if (count($errors) == 0) {
                        $em->persist($solution);
                        $em->flush();

                        $response = new Response(json_encode(array("type" => "solution",
                                            "id" => $solution->getId(),
                                            "libelle" => $solution->getLibelle(),
                                            "existe" => $solution->getExiste()
                                                )
                                        )
                        );
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    } else {
                        $response = new Response(json_encode(array("type" => "error",
                                            "libelle" => $errors)));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                }
            } else {
                $response = new Response(json_encode(array("type" => "error",
                                    "libelle" => "Type du noeud parent non géré par le contrôleur. Id du parent=" . $parent_id)));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }
    }

    /**
     * @Route("/Ajax/Liaisons/Treeview/Remove", name="admin_ajax_liaisonstreeview_remove")
     */
    public function admin_ajax_liaisonsTreeviewRemoveAction() {
        $request = $this->getRequest();
        $id = $request->request->get('id');
        $id = substr($id, 1, strlen($id) - 1);
        $type = $request->get('type');

        //initialisation de la réponse avec un succès. En cas d'erreur, la variable sera systématiquement redéfinie.
        $response = new Response(json_encode(array("type" => "success", "libelle" => "")));

        if (!$id || !$type) {
            $response = new Response("Erreur de passage de paramètres", 400);
        } else {

            $em = $this->getDoctrine()->getEntityManager();

            switch ($type) {
                case "root":
                    $response = new Response("Suppression du noeud racine interdite.", 400);
                    break;
                case "entite":
                    $entite = $em->getRepository('solversolverBundle:entites')->find($id);
                    if (!$entite) {
                        $response = new Response("Entité introuvable", 400);
                    } else {
                        $entite->setExiste(false);
                        $em->persist($entite);
                        $em->flush();
                    }
                    break;
                case "probleme":
                    $probleme = $em->getRepository('solversolverBundle:problemes')->find($id);
                    if (!$probleme) {
                        $response = new Response("Problème introuvable", 400);
                    } else {
                        $probleme->setExiste(false);
                        $em->persist($probleme);
                        $em->flush();
                    }
                    break;
                case "solution":
                    $solution = $em->getRepository('solversolverBundle:solutions')->find($id);
                    if (!$solution) {
                        $response = new Response("Solution introuvable", 400);
                    } else {
                        $solution->setExiste(false);
                        $em->persist($solution);
                        $em->flush();
                    }
                    break;
                default:
                    $response = new Response("Type de noeud inconnu ou non géré par le contrôleur.", 501);
                    break;
            }
        }
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/Ajax/Liaisons/Treeview/Check", name="admin_ajax_liaisonstreeview_check")
     */
    public function admin_ajax_liaisonsTreeviewCheckAction() {
        $request = $this->getRequest();
        $id = $request->request->get('id');
        $id = substr($id, 1, strlen($id) - 1);
        $type = $request->get('type');
        $value = $request->get('value');

        //initialisation de la réponse avec un succès. En cas d'erreur, la variable sera systématiquement redéfinie.
        $response = new Response(json_encode(array("type" => "success", "libelle" => "")));

        if (!$id || !$type || !$value) {
            $response = new Response("Erreur de passage de paramètres", 400);
        } else {
            $em = $this->getDoctrine()->getEntityManager();
            if ($value == 'true') {
                $value = true;
            } else {
                $value = false;
            }  //les paramètres sont envoyés en string, il faut convertir en booléen pour la valeur de la checkbox
            //placé ici, car le tests des paramètres ne passe pas si la valeur est fausse.

            switch ($type) {
                case "root":
                    $response = new Response("Modification du noeud racine interdite.", 400);
                    break;
                case "entite":
                    $entite = $em->getRepository('solversolverBundle:entites')->find($id);
                    if (!$entite) {
                        $response = new Response("Entité introuvable", 400);
                    } else {
                        $entite->setAffiche($value);
                        $em->persist($entite);
                        $em->flush();
                    }
                    break;
                case "probleme":
                    $probleme = $em->getRepository('solversolverBundle:problemes')->find($id);
                    if (!$probleme) {
                        $response = new Response("Problème introuvable", 400);
                    } else {
                        $probleme->setAffiche($value);
                        $em->persist($probleme);
                        $em->flush();
                    }
                    break;
                case "solution":
                    $solution = $em->getRepository('solversolverBundle:solutions')->find($id);
                    if (!$solution) {
                        $response = new Response("Solution introuvable", 400);
                    } else {
                        $solution->setAffiche($value);
                        $em->persist($solution);
                        $em->flush();
                    }
                    break;
                default:
                    $response = new Response("Type de noeud inconnu ou non géré par le contrôleur.", 501);
                    break;
            }
        }

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Affichage du détail du noeud sélectionné
     * 
     * @Route("/Ajax/Liaisons/Treeview/NodeDetail", name="admin_ajax_liaisonstreeview_nodedetail")
     */
    public function admin_ajax_liaisonsTreeview_NodeDetail_Action() {
        $request = $this->getRequest();
        $id = $request->request->get('id');
        $id = substr($id, 1, strlen($id) - 1);
        $type = $request->get('type');

        if (!$id || !$type) {
            $response = new Response("Erreur de passage de paramètres", 400);
            return $response;
        } else {
            
            $em=$this->getDoctrine()->getEntityManager();
            
            switch ($type) {
                case "root":
                    return $this->render('solversolverBundle:Admin/Ajax/Treeview/node_detail:root.html.twig');
                    break;
                case "entite":
                    $entite = $em->getRepository('solversolverBundle:entites')->find($id);                    
                    if (!$entite) {
                        $response = new Response("Entité introuvable", 400);
                        return $response;
                    } else {
                        $form = $this->createForm(new entitesType(), $entite);
                        return $this->render('solversolverBundle:Admin/Ajax/Treeview/node_detail:entite.html.twig',array('entite'=>$entite,
                                                                                                                         'form'=>$form->createView()
                                                                                                                        )
                                            );
                    }
                    break;
                case "probleme":
                    $probleme = $em->getRepository('solversolverBundle:problemes')->find($id);                    
                    if (!$probleme) {
                        $response = new Response("Problème introuvable", 400);
                        return $response;
                    } else {
                        $form = $this->createForm(new problemesType(), $probleme);
                        return $this->render('solversolverBundle:Admin/Ajax/Treeview/node_detail:probleme.html.twig',array('probleme'=>$probleme,'form'=>$form->createView()));
                    }
                    break;
                case "solution":
                    $solution = $em->getRepository('solversolverBundle:solutions')->find($id);                    
                    if (!$solution) {
                        $response = new Response("Solution introuvable", 400);
                        return $response;
                    } else {
                        $form = $this->createForm(new solutionsType(), $solution);
                        return $this->render('solversolverBundle:Admin/Ajax/Treeview/node_detail:solution.html.twig',array('solution'=>$solution,'form'=>$form->createView()));
                    }
                    break;
                default:
                    $response = new Response("Type de noeud inconnu ou non géré par le contrôleur.", 501);
                    return $response;
                    break;
            }
        }
    }

}
