<?php

namespace Flower\DriveBundle\Service;

use Flower\DriveBundle\Entity\File;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Created by PhpStorm.
 * User: juanma
 * Date: 4/16/16
 * Time: 6:13 PM
 */
class FileService implements ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function uploadFile(File $file)
    {
        /* the file property can be empty if the field is not required */
        if (null === $file->getFile()) {
            return $file;
        }

        $uploadBaseDir = $this->container->getParameter("uploads_base_dir");
        $uploadDir = $this->container->getParameter("drive_dir");

        /* set the path property to the filename where you've saved the file */
        $filename = $file->getFile()->getClientOriginalName();
        $extension = $file->getFile()->getClientOriginalExtension();

        $imageName = md5($filename . time()) . '.' . $extension;

        $file->setPath($uploadDir . $imageName);
        $file->setType($extension);
        $file->getFile()->move($uploadBaseDir . $uploadDir, $imageName);

        $file->setFile(null);

        return $file;
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}