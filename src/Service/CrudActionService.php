<?php

namespace App\Service;

use App\DTO\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class CrudActionService
{
    public function __construct(
        private Environment               $environment,
        private CsrfTokenManagerInterface $csrfTokenManager,
        private RequestStack              $requestStack,
        private RouterInterface           $router,
        private FormFactoryInterface      $formFactory
    )
    {
    }

    /**
     * @param Request $request
     * @param ServiceEntityRepository $repository
     * @param string $findMethod
     * @param string $templateDir
     * @param array $vars
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function indexAction(
        Request                 $request,
        ServiceEntityRepository $repository,
        string                  $findMethod,
        string                  $templateDir,
        array                   $vars = []
    ): Response
    {
        $filter = $request->query->get('filter', '');
        $amountPerPage = $request->query->get('amount', 10);
        $pageNumber = $request->query->get('page', 1);

        $data = call_user_func_array([$repository, $findMethod], [$filter, $amountPerPage, $pageNumber]);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->from() > $paginator->getTotal()) {
            $number = ($pageNumber === 1) ? 1 : ($pageNumber - 1);
            return new RedirectResponse($this->router->generate($request->attributes->get('_route'), [...$request->query->all(), 'page' => $number]), Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_list.html.twig' : 'index.html.twig';

        $template = $this->environment->render("$templateDir/$template", [
                'filter' => $filter,
                'paginator' => $paginator
            ] + $vars);
        return new Response($template);
    }

    /**
     * @param Request $request
     * @param object $entity
     * @param string $templateDir
     * @param string $type
     * @param string $title
     * @param array $vars
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function showAction(
        Request $request,
        object  $entity,
        string  $templateDir,
        string  $type,
        string  $title,
        array   $vars = []
    ): Response
    {
        $template = ($request->isXmlHttpRequest()) ? '_detail.html.twig' : 'show.html.twig';
        $parseTemplate = $this->environment->render("$templateDir/$template", [
                $type => $entity,
                'title' => $title
            ] + $vars);
        return new Response($parseTemplate);
    }

    /**
     * @param Request $request
     * @param ServiceEntityRepository $repository
     * @param object $entity
     * @param string $successMsg
     * @param string $gotTo
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function deleteAction(
        Request                 $request,
        ServiceEntityRepository $repository,
        object                  $entity,
        string                  $successMsg,
        string                  $gotTo
    ): Response
    {
        if ($this->csrfTokenManager->isTokenValid(new CsrfToken('delete' . $entity->getId(), $request->getPayload()->getString('_token')))) {
            $id = 'delete_' . $this->getClassName($entity::class) . '_' . $entity->getId();
            try {
                $repository->remove($entity, true);

                if ($request->isXmlHttpRequest()) {
                    $template = $this->environment->render("partials/_form_success.html.twig", [
                        'id' => $id,
                        'type' => 'text-bg-success',
                        'message' => $successMsg
                    ]);
                    return new Response($template);
                }
            } catch (\Exception $exception) {
                if ($request->isXmlHttpRequest()) {
                    $template = $this->environment->render("partials/_form_success.html.twig", [
                        'id' => $id,
                        'type' => 'text-bg-danger',
                        'message' => $exception->getMessage()
                    ]);
                    return new Response($template);
                }
            }
        }

        $this->requestStack->getSession()->getFlashBag()->add('success', $successMsg);
        return new RedirectResponse($this->router->generate($gotTo), Response::HTTP_SEE_OTHER);
    }

    /**
     * @param Request $request
     * @param ServiceEntityRepository $repository
     * @param object $entity
     * @param string $formType
     * @param string $action
     * @param string $successMsg
     * @param string $gotTo
     * @param string $templateDir
     * @param array $vars
     * @param bool $modal
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function newAction(
        Request                 $request,
        ServiceEntityRepository $repository,
        object                  $entity,
        string                  $formType,
        string                  $action,
        string                  $successMsg,
        string                  $gotTo,
        string                  $templateDir,
        array                   $vars = [],
        bool                    $modal = false
    ): Response
    {
        $form = $this->formFactory->create($formType, $entity, [
            'action' => $action,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $repository->save($entity, true);

            //si el formulario se mando correctamente por ajax
            if ($request->isXmlHttpRequest()) {
                $template = $this->environment->render("partials/_form_success.html.twig", [
                    'id' => 'new_' . $this->getClassName($entity::class) . '_' . $entity->getId(),
                    'type' => 'text-bg-success',
                    'message' => $successMsg
                ]);
                return new Response($template);
            }

            $this->requestStack->getSession()->getFlashBag()->add('success', $successMsg);
            return new RedirectResponse($this->router->generate($gotTo), Response::HTTP_SEE_OTHER);
        }

//        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : (($modal) ? '_form_fields.html.twig' : 'new.html.twig');
        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : (($modal) ? '_form.html.twig' : 'new.html.twig');//comportamiento por controlador
        return new Response($this->environment->render("$templateDir/$template", [
                $templateDir => $entity,
                'form' => $form->createView(),
            ] + $vars), ($form->isSubmitted() && !$form->isValid()) ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param ServiceEntityRepository $repository
     * @param object $entity
     * @param string $formType
     * @param string $action
     * @param string $successMsg
     * @param string $gotTo
     * @param string $templateDir
     * @param array $vars
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function editAction(
        Request                 $request,
        ServiceEntityRepository $repository,
        object                  $entity,
        string                  $formType,
        string                  $action,
        string                  $successMsg,
        string                  $gotTo,
        string                  $templateDir,
        array                   $vars = []
    ): Response
    {
        $form = $this->formFactory->create($formType, $entity, [
            'action' => $action,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repository->flush();

            //si el formulario se mando correctamente por ajax
            if ($request->isXmlHttpRequest()) {
                $template = $this->environment->render("partials/_form_success.html.twig", [
                    'id' => 'edit_' . $this->getClassName($entity::class) . '_' . $entity->getId(),
                    'type' => 'text-bg-success',
                    'message' => $successMsg
                ]);
                return new Response($template);
            }

            $this->requestStack->getSession()->getFlashBag()->add('success', $successMsg);
            return new RedirectResponse($this->router->generate($gotTo), Response::HTTP_SEE_OTHER);
        }

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'edit.html.twig';
        return new Response($this->environment->render("$templateDir/$template", [
                $templateDir => $entity,
                'form' => $form->createView(),
            ] + $vars), ($form->isSubmitted() && !$form->isValid()) ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param object $entity
     * @param string $templateDir
     * @param array $vars
     * @param bool $modal
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function formLiveComponentAction(
        Request $request,
        object  $entity,
        string  $templateDir,
        array   $vars = [],
        bool    $modal = false
    ): Response
    {
        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : (($modal) ? '_form.html.twig' : ($entity->getid() ? 'edit.html.twig' : 'new.html.twig'));//comportamiento por controlador
        return new Response($this->environment->render("$templateDir/$template", [
                $templateDir => $entity,
            ] + $vars));
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function options(Request $request, object $entity, array $entities): Response
    {
        if ($request->isXmlHttpRequest()) {
            return new Response($this->environment->render('partials/_select_options.html.twig', [
                'entities' => $entities,
                'selected' => $entity->getId()
            ]));
        }

        throw new BadRequestHttpException('Ajax request');
    }

    /**
     * @param $classname
     * @return false|int|string
     */
    private function getClassName($classname): false|int|string
    {
        if ($pos = strrpos($classname, '\\')) return substr($classname, $pos + 1);
        return $pos;
    }
}