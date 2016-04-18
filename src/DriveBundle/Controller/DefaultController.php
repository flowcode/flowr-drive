<?php

namespace Flower\DriveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();

        $folders = $em->getRepository('FlowerDriveBundle:Folder')->findBy(array(
            "parent" => null
        ));
        $files = $em->getRepository('FlowerDriveBundle:File')->findBy(array(
            "folder" => null
        ));

        return $this->render('FlowerDriveBundle:Default:index.html.twig', array(
            'folders' => $folders,
            'files' => $files,
        ));
    }
}
