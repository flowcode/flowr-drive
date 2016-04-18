<?php

namespace Flower\DriveBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Flower\DriveBundle\Entity\File;
use Flower\DriveBundle\Form\Type\FileType;
use Doctrine\ORM\QueryBuilder;

/**
 * File controller.
 *
 */
class FileController extends Controller
{
    /**
     * Lists all File entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('FlowerDriveBundle:File')->findAll();
        return $this->render('FlowerDriveBundle:File:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a File entity.
     *
     */
    public function showAction(File $file)
    {
        $editForm = $this->createForm(new FileType(), $file, array(
            'action' => $this->generateUrl('drive_file_update', array('id' => $file->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($file->getId(), 'drive_file_delete');

        return $this->render('FlowerDriveBundle:File:show.html.twig', array(
            'file' => $file,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        ));
    }

    /**
     * Displays a form to create a new File entity.
     *
     */
    public function newAction(Request $request)
    {
        $file = new File();

        if ($request->get('folder_id')) {
            $em = $this->getDoctrine()->getManager();
            $folder = $em->getRepository('FlowerDriveBundle:Folder')->find($request->get('folder_id'));
            $file->setFolder($folder);
        }

        $form = $this->createForm(new FileType(), $file);

        return $this->render('FlowerDriveBundle:File:new.html.twig', array(
            'file' => $file,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new File entity.
     *
     */
    public function createAction(Request $request)
    {
        $file = new File();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new FileType(), $file);
        if ($form->handleRequest($request)->isValid()) {

            $file = $this->get('drive.service.file')->uploadFile($file);

            $em->persist($file);
            $em->flush();

            if ($file->getFolder()) {
                return $this->redirect($this->generateUrl('drive_folder_browse', array(
                    'id' => $file->getFolder()->getId()
                )));
            }

            return $this->redirect($this->generateUrl('drive_file_show', array('id' => $file->getId())));
        }

        return $this->render('FlowerDriveBundle:File:new.html.twig', array(
            'file' => $file,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing File entity.
     *
     */
    public function editAction(File $file)
    {
        $editForm = $this->createForm(new FileType(), $file, array(
            'action' => $this->generateUrl('drive_file_update', array('id' => $file->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($file->getId(), 'drive_file_delete');

        return $this->render('FlowerDriveBundle:File:edit.html.twig', array(
            'file' => $file,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing File entity.
     *
     */
    public function updateAction(File $file, Request $request)
    {
        $editForm = $this->createForm(new FileType(), $file, array(
            'action' => $this->generateUrl('drive_file_update', array('id' => $file->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {


            $file = $this->get('drive.service.file')->uploadFile($file);

            $this->getDoctrine()->getManager()->flush();

            if ($file->getFolder()) {
                return $this->redirect($this->generateUrl('drive_folder_browse', array(
                    'id' => $file->getFolder()->getId()
                )));
            }

            return $this->redirect($this->generateUrl('drive_file_show', array('id' => $file->getId())));
        }
        $deleteForm = $this->createDeleteForm($file->getId(), 'drive_file_delete');

        return $this->render('FlowerDriveBundle:File:edit.html.twig', array(
            'file' => $file,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a File entity.
     *
     */
    public function deleteAction(File $file, Request $request)
    {
        $form = $this->createDeleteForm($file->getId(), 'drive_file_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($file);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('drive_file'));
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
