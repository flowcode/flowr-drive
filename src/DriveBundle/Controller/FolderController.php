<?php

namespace Flower\DriveBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Flower\DriveBundle\Entity\Folder;
use Flower\DriveBundle\Form\Type\FolderType;
use Doctrine\ORM\QueryBuilder;

/**
 * Folder controller.
 *
 */
class FolderController extends Controller
{
    /**
     * Lists all Folder entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FlowerDriveBundle:Folder')->findAll();
        return $this->render('FlowerDriveBundle:folder:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Folder entity.
     *
     */
    public function browseAction(Folder $folder)
    {

        $em = $this->getDoctrine()->getManager();

        $folders = $em->getRepository('FlowerDriveBundle:Folder')->findBy(array(
            'parent' => $folder,
            'archived' => false,
        ));
        $files = $em->getRepository('FlowerDriveBundle:File')->findBy(array(
            'folder' => $folder,
            'archived' => false,
        ));

        return $this->render('FlowerDriveBundle:folder:browse.html.twig', array(
            'folder' => $folder,
            'folders' => $folders,
            'files' => $files,
        ));
    }

    /**
     * Finds and displays a Folder entity.
     *
     */
    public function showAction(Folder $folder)
    {
        $editForm = $this->createForm(new FolderType(), $folder, array(
            'action' => $this->generateUrl('drive_folder_update', array('id' => $folder->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($folder->getId(), 'drive_folder_delete');

        return $this->render('FlowerDriveBundle:folder:show.html.twig', array(
            'folder' => $folder,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new Folder entity.
     *
     */
    public function newAction(Request $request)
    {
        $folder = new Folder();

        if ($request->get('parent_id')) {
            $em = $this->getDoctrine()->getManager();
            $parentFolder = $em->getRepository('FlowerDriveBundle:Folder')->find($request->get('parent_id'));
            $folder->setParent($parentFolder);
        }

        $form = $this->createForm(new FolderType(), $folder);

        return $this->render('FlowerDriveBundle:folder:new.html.twig', array(
            'folder' => $folder,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new Folder entity.
     *
     */
    public function createAction(Request $request)
    {
        $folder = new Folder();
        $form = $this->createForm(new FolderType(), $folder);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($folder);
            $em->flush();

            if ($folder->getParent()) {
                return $this->redirect($this->generateUrl('drive_folder_browse', array(
                    'id' => $folder->getParent()->getId()
                )));
            }

            return $this->redirect($this->generateUrl('drive_folder_show', array('id' => $folder->getId())));
        }

        return $this->render('FlowerDriveBundle:folder:new.html.twig', array(
            'folder' => $folder,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Folder entity.
     *
     */
    public function editAction(Folder $folder)
    {
        $editForm = $this->createForm(new FolderType(), $folder, array(
            'action' => $this->generateUrl('drive_folder_update', array('id' => $folder->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($folder->getId(), 'drive_folder_delete');

        return $this->render('FlowerDriveBundle:folder:edit.html.twig', array(
            'folder' => $folder,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Folder entity.
     *
     */
    public function updateAction(Folder $folder, Request $request)
    {
        $editForm = $this->createForm(new FolderType(), $folder, array(
            'action' => $this->generateUrl('drive_folder_update', array('id' => $folder->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            if ($folder->getParent()) {
                return $this->redirect($this->generateUrl('drive_folder_browse', array(
                    'id' => $folder->getParent()->getId()
                )));
            }

            return $this->redirect($this->generateUrl('drive_folder_show', array('id' => $folder->getId())));
        }
        $deleteForm = $this->createDeleteForm($folder->getId(), 'drive_folder_delete');

        return $this->render('FlowerDriveBundle:folder:edit.html.twig', array(
            'folder' => $folder,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Folder entity.
     *
     */
    public function deleteAction(Folder $folder, Request $request)
    {
        $form = $this->createDeleteForm($folder->getId(), 'drive_folder_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($folder);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('drive_folder'));
    }

    /**
     * Archive a Folder entity.
     *
     */
    public function archiveAction(Folder $folder, Request $request)
    {
        $folder->setArchived(true);

        $this->getDoctrine()->getManager()->flush();

        if ($folder->getParent()) {
            return $this->redirect($this->generateUrl('drive_folder_browse', array(
                'id' => $folder->getParent()->getId()
            )));
        }

        return $this->redirect($this->generateUrl('drive_folder'));
    }

    /**
     * Create Delete form
     *
     * @param integer $id
     * @param string $route
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
    }

}
