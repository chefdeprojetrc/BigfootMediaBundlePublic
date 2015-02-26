<?php

namespace Bigfoot\Bundle\MediaBundle\Controller;

use Bigfoot\Bundle\MediaBundle\Entity\MediaRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Doctrine\ORM\AbstractQuery;

use Bigfoot\Bundle\CoreBundle\Controller\BaseController;
use Bigfoot\Bundle\CoreBundle\Entity\Tag;
use Bigfoot\Bundle\MediaBundle\Entity\Media;
use Bigfoot\Bundle\MediaBundle\Form\MediaType;
use Bigfoot\Bundle\MediaBundle\Form\PortfolioSearchData;

/**
 * Bigfoot MediaController. Implements the routes necessary to display the media management module.
 *
 * @Cache(maxage="0", smaxage="0", public="false")
 * @Route("/portfolio")
 */
class MediaController extends BaseController
{
    /**
     * Displays the list of persisted medias.
     *
     * @Route("/", name="portfolio_dashboard")
     * @Method("GET")
     * @Template("BigfootMediaBundle::portfolio.html.twig")
     */
    public function portfolioDashboardAction()
    {
        return array();
    }

    /**
     * Displays the popin used for media selection in forms
     *
     * @Route("/popin/{id}", name="portfolio_popin", defaults={"id"=null})
     * @Method("GET")
     * @Template("BigfootMediaBundle::popin.html.twig")
     */
    public function popinAction($id)
    {
        $em = $this->container->get('doctrine')->getManager();
        $mediaRepository  = $em->getRepository('BigfootMediaBundle:Media');
        $orderedMedias    = array();
        $selectedMediaIds = array();
        $allMedias        = $mediaRepository->findBy(array(), null, '50');

        if ($id) {
            $selectedMediaIds = explode(';', $id);
            $query = $em->createQuery(
                'SELECT m
                FROM BigfootMediaBundle:Media m
                WHERE m.id IN (:ids)'
            )->setParameter('ids', $selectedMediaIds);
            $selectedMedias = $query->getResult();
            $orderedMedias = array_flip($selectedMediaIds);

            foreach ($selectedMedias as $selectedMedia) {
                $orderedMedias[$selectedMedia->getId()] = $selectedMedia;
            }
        }

        $searchData = new PortfolioSearchData();
        $searchForm = $this->container->get('form.factory')->create('bigfoot_portfolio_search', $searchData);

        return array(
            'allMedias'         => $allMedias,
            'selectedMedias'    => $orderedMedias,
            'mediaIds'          => $selectedMediaIds,
            'form'              => $searchForm->createView(),
        );
    }

    /**
     * Displays an edit form for a media object
     *
     * @Route("/popin/{id}/edit", name="portfolio_edit")
     * @Method({"GET", "POST"})
     * @Template("BigfootMediaBundle:snippets:edit.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->container->get('doctrine')->getManager();
        $theme = $this->container->get('bigfoot.theme');
        $themeBundle = $theme->getTwigNamespace();

        $media = $this->getMedia($id);
        $file = $media->getFile();

        $form = $this->container->get('form.factory')->create('bigfoot_media_mediatype', $media);
        if ('POST' == $request->getMethod()) {
            $form->submit($request);

            if ($form->isValid()) {
                $em->persist($media);
                $em->flush();

                $media->setFile($file);

                return new Response(json_encode(array(
                    'id'        => $id,
                    'success'   => true,
                    'object'    => $media,
                    'html'      => $this->container->get('twig')->render($themeBundle.':snippets:table_line.html.twig', array(
                        'line' => $media,
                        'used' => false,
                    )),
                )), 200, array('Content-Type', 'application/json'));
            }
        }

        return new Response(json_encode(array(
            'html'  => $this->container->get('twig')->render($themeBundle.':snippets:edit.html.twig', array(
                'form'  => $form->createView(),
                'media' => $media,
                'id'    => $id,
            )),
        )), 200, array('Content-Type', 'application/json'));
    }

    /**
     * Handles deletion
     *
     * @Route("/popin/{id}/delete", name="portfolio_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->container->get('doctrine')->getManager();

        $media = $this->getMedia($id);

        $em->remove($media);
        $em->flush();

        return new Response(json_encode(array(
            'id' => $id,
            'success' => true,
        )), 200, array('Content-Type', 'application/json'));
    }

    /**
     * Handles upload
     *
     * @Route("/upload", name="portfolio_upload")
     */
    public function uploadAction(Request $request)
    {
        // retrieves the posted data, for reference
        $file = $request->get('value');
        $name = $request->get('name');

        $json = array(
            'name'    => $name,
            'success' => false,
            'html'    => null,
        );

        // get the mime
        $getMime = explode('.', $name);
        $mime    = end($getMime);

        // separete out the data
        $data = explode(',', $file);

        // encode it correctly
        $encodedData = str_replace(' ', '+', $data[1]);
        $decodedData = base64_decode($encodedData);

        $media = new Media();

        // generate new name, relative and absolute path
        $fileManager = $this->container->get('bigfoot_core.manager.file_manager');
        $name = $fileManager::sanitizeName($name);
        $image = uniqid().'_'.$name;
        $directory = $this->getUploadDir();
        $absolutePath = $directory.'/'.$image;

        if (!file_exists($directory)) {
            $filesystem = $this->get('filesystem');
            $filesystem->mkdir($directory, 0777);
        }

        if (file_put_contents($absolutePath, $decodedData)) {
            $relativePath = $this->container->getParameter('bigfoot.core.upload_dir').$this->container->getParameter('bigfoot.media.portfolio_dir').$image;
            $imageInfos   = getimagesize($absolutePath);
            if (!isset($imageInfos['channels']) or $imageInfos['channels'] == 3) { // channels sera 3 pour des images RGB et 4 pour des images CMYK.
                $media
                    ->setType($imageInfos['mime'])
                    ->setFile($relativePath)
                ;

                $em = $this->container->get('doctrine')->getManager();

                $em->persist($media);
                $em->flush();

                $mediaRepository = $em->getRepository('BigfootMediaBundle:Media');

                $mediaRepository->setMetadata($media, 'title', $name);
                $mediaRepository->setMetadata($media, 'width', $imageInfos[0]);
                $mediaRepository->setMetadata($media, 'height', $imageInfos[1]);
                $mediaRepository->setMetadata($media, 'size', $media->convertFileSize(filesize($absolutePath)));

                $em->flush();

                $json['success'] = true;
                $json['html'] = $this->container->get('twig')->render('BigfootMediaBundle:snippets:table_line.html.twig', array(
                    'line' => $media,
                    'used' => false,
                ));
            } else {
                $json['html'] = $this->container->get('twig')->render('BigfootMediaBundle:snippets:unused.html.twig');
                unlink($absolutePath);
            }
        }

        return new Response(json_encode($json), 200, array('Content-Type', 'application/json'));
    }

    /**
     * Add a tag
     *
     * @Route("/tag/add", name="portfolio_tag_add")
     */
    public function addTagAction(Request $request)
    {
        $em = $this->container->get('doctrine')->getManager();

        $tag = new Tag();
        $tag->setName($request->get('tag'));

        $em->persist($tag);
        $em->flush();

        return new Response(json_encode(
            array(
                'html' => $this->container->get('twig')->render('BigfootMediaBundle:snippets:tag_option.html.twig', array(
                        'tag' => $tag,
                )),
            )
        ), 200, array('Content-Type', 'application/json'));
    }

    /**
     * @Route("/list-fields", name="portfolio_list_fields")
     */
    public function listFieldsAction(Request $request)
    {
        $em = $this->container->get('doctrine')->getManager();
        $table = $request->get('table', '');

        $query = $em->createQuery(
            'SELECT mu.column_ref
            FROM BigfootMediaBundle:MediaUsage mu
            WHERE mu.tableRef = :table'
        )->setParameter('table', $table);
        $columns = $query->getResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);

        $columnChoices = array();
        foreach ($columns as $column) {
            $columnChoices[$column] = $column;
        }

        return new Response(json_encode($columnChoices), 200, array('Content-Type', 'application/json'));
    }

    /**
     * @Route("/popin/search", name="portfolio_search")
     */
    public function searchAction(Request $request)
    {
        $arrayParams = array();
        $ids = $request->get('ids');
        $theme = $this->container->get('bigfoot.theme');
        $themeBundle = $theme->getTwigNamespace();

        $searchData = new PortfolioSearchData();
        $searchForm = $this->container->get('form.factory')->create('bigfoot_portfolio_search', $searchData);

        $searchForm->submit($request);

        if ($searchForm->isValid()) {

            $em = $this->container->get('doctrine')->getManager();
            $queryBuilder = $em->createQueryBuilder()
                ->select('DISTINCT m')
                ->from('BigfootMediaBundle:Media', 'm')
                ->leftJoin('m.metadatas', 'md')
                ->leftJoin('m.tags', 't');

            if ($searchData->getSearch()) {
                $queryString = sprintf('%%%s%%', $searchData->getSearch());
                $arrayParams[':queryString'] = $queryString;
                $queryBuilder->where('md.value LIKE :queryString OR m.file LIKE :queryString OR t.name LIKE :queryString');
            }

            if ($table = $searchData->getTable()) {
                $queryBuilder
                    ->join('m.usages', 'mu')
                    ->andWhere('mu.tableRef = :table');

                $arrayParams[':table'] = $table;

                if ($column = $searchData->getColumn()) {
                    $arrayParams[':column'] = $column;
                    $queryBuilder
                        ->andWhere('mu.column_ref = :column');
                }
            }

            $query = $queryBuilder->getQuery();

            $query->setParameters($arrayParams);

            $selectedMedias = $query->getResult();

            return new Response(json_encode(array(
                'success' => true,
                'html' => $this->container->get('twig')->render($themeBundle.':snippets:table.html.twig', array(
                    'allMedias' => $selectedMedias,
                    'mediaIds' => explode(';', $ids),
                )),
            )), 200, array('Content-Type', 'application/json'));
        }
    }

    /**
     * @Route("/ck/upload", name="bigfoot_media_upload", options={"expose"=true})
     */
    public function ckUploadAction(Request $request)
    {
        $content = '';
        /** @var UploadedFile $file */
        if ($file = $request->files->get('upload', false)) {
            try {
                $fileName = $file->getClientOriginalName();
                $mimeType = $file->getMimeType();
                $size = $file->getSize();
                $absPath = sprintf('%s/%s', rtrim($this->getUploadDir(), '/'), $request->get('CKEditor'));
                $relPath = sprintf('%s/%s', rtrim($this->getUploadDir(false), '/'), $request->get('CKEditor'));
                $file->move($absPath, $fileName);
                $content = sprintf("window.parent.CKEDITOR.tools.callFunction(%s, '%s', '%s')",
                    $request->get('CKEditorFuncNum'),
                    sprintf('%s/%s', $relPath, $fileName),
                    ''
                );

                $media = new Media();
                $media->setFile(sprintf('%s/%s', $relPath, $fileName));
                $media->setType($mimeType);

                $em = $this->getDoctrine()->getManager();

                $em->persist($media);
                $em->flush();

                /** @var MediaRepository $mediaRepository */
                $mediaRepository = $em->getRepository('BigfootMediaBundle:Media');

                list($width, $height) = getimagesize(sprintf('%s/%s', rtrim($absPath, '/'), $fileName));
                $mediaRepository->setMetadata($media, 'title', $fileName);
                $mediaRepository->setMetadata($media, 'width', $width);
                $mediaRepository->setMetadata($media, 'height', $height);
                $mediaRepository->setMetadata($media, 'size', $media->convertFileSize($size));

                $em->flush();
            } catch (\Exception $e) {
                $content = sprintf('alert(\'%s\')', $e->getMessage());
            }
        }

        return new Response(sprintf('<script>%s</script>', $content));
    }

    /**
     * Get media object from ID
     *
     * @param $id
     * @return Media
     */
    private function getMedia($id)
    {
        $em = $this->container->get('doctrine')->getManager();
        $mediaRepository = $em->getRepository('BigfootMediaBundle:Media');

        return $mediaRepository->find($id);
    }

    /**
     * @return string
     */
    private function getUploadDir($absolute = true)
    {
        $dir = '';
        if ($absolute) {
            $dir .= $this->container->get('kernel')->getRootDir() . '/../web';
        }
        return rtrim($dir, '/').sprintf('/%s/%s', trim($this->container->getParameter('bigfoot.core.upload_dir'), '/'), trim($this->container->getParameter('bigfoot.media.portfolio_dir'), '/'));
    }
}
