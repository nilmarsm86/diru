<?php

namespace App\Service;

use App\DTO\Paginator;
use App\Repository\FilterInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
//        private RequestStack              $requestStack,
        private RouterInterface           $router,
//        private FormFactoryInterface      $formFactory,
//        private FlashBagInterface         $flashBag
    )
    {
    }

    /**
     * @param Request $request
     * @param FilterInterface $repository
     * @param string $findMethod
     * @param string $templateDir
     * @param array<mixed> $vars
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function indexAction(
        Request                 $request,
        FilterInterface $repository,
        string                  $findMethod,
        string                  $templateDir,
        array                   $vars = []
    ): Response
    {
//        $filter = $request->query->get('filter', '');
//        $amountPerPage = $request->query->get('amount', 10);
//        $pageNumber = $request->query->get('page', 1);
        /** @var string $filter */
        /** @var int $amountPerPage */
        /** @var int $pageNumber */
        list($filter, $amountPerPage, $pageNumber) = $this->getManageQuerys($request);

        $callback = [$repository, $findMethod];
        assert(is_callable($callback));
        /** @var array<mixed> $data */
        $data = call_user_func_array($callback, [$filter, $amountPerPage, $pageNumber]);

        $paginator = new Paginator($data, $amountPerPage, $pageNumber);
        if ($paginator->from() > $paginator->getTotal()) {
//            $number = ($pageNumber === 1) ? 1 : ($pageNumber - 1);
//            return new RedirectResponse($this->router->generate($request->attributes->get('_route'), [...$request->query->all(), 'page' => $number]), Response::HTTP_SEE_OTHER);
            return $paginator->greatherThanTotal($request, $this->router, $pageNumber);
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
     * @return array<mixed>
     */
    public function getManageQuerys(Request $request): array
    {
        /** @var string $filter */
        $filter = $request->query->get('filter', '');
        /** @var int $amountPerPage */
        $amountPerPage = $request->query->get('amount', '10');
        /** @var int $pageNumber */
        $pageNumber = $request->query->get('page', '1');

        return [$filter, $amountPerPage, $pageNumber];
    }

    /**
     * @param Request $request
     * @param object $entity
     * @param string $templateDir
     * @param string $type
     * @param string $title
     * @param array<mixed> $vars
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
     * @param object $repository
     * @param object $entity
     * @param string $successMsg
     * @param string $gotTo
     * @param array<mixed> $goToParams
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
//    public function deleteAction(
//        Request $request,
//        object  $repository,
//        object  $entity,
//        string  $successMsg,
//        string  $gotTo,
//        array   $goToParams = []
//    ): Response
//    {
//        if ($this->csrfTokenManager->isTokenValid(new CsrfToken('delete' . $entity->getId(), $request->getPayload()->getString('_token')))) {
//            $id = 'delete_' . $this->getClassName($entity::class) . '_' . $entity->getId();
//            try {
//                $repository->remove($entity, true);
//
//                if ($request->isXmlHttpRequest()) {
//                    $template = $this->environment->render("partials/_form_success.html.twig", [
//                        'id' => $id,
//                        'type' => 'text-bg-success',
//                        'message' => $successMsg
//                    ]);
//
//                    return new Response($template);
//                }
//            } catch (\Exception $exception) {
//                if ($request->isXmlHttpRequest()) {
//                    $template = $this->environment->render("partials/_form_success.html.twig", [
//                        'id' => $id,
//                        'type' => 'text-bg-danger',
//                        'message' => $exception->getMessage()
//                    ]);
//                    return new Response($template);
//                }
//            }
//        }
////        $this->requestStack->getSession()->getFlashBag()->add('success', $successMsg);
//        return new RedirectResponse($this->router->generate($gotTo, $goToParams), Response::HTTP_SEE_OTHER);
//    }

    public function deleteAction(
        Request $request,
        object  $repository,
        object  $entity,
        string  $successMsg,
        string  $gotTo,
        array   $goToParams = []
    ): Response
    {
        $callback = [$entity, 'getId'];
        assert(is_callable($callback));
        /** @var string $entityId */
        $entityId = call_user_func_array($callback, []);

        $isAjax = $request->isXmlHttpRequest();
        $id = 'delete_' . $this->getClassName($entity::class) . '_' . $entityId;

        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('delete' . $entityId, $request->getPayload()->getString('_token')))) {
            if ($isAjax) {
                return new Response($this->environment->render("partials/_form_success.html.twig", [
                    'id' => $id,
                    'type' => 'text-bg-danger',
                    'message' => 'Token CSRF inválido'
                ]));
            }
            return new RedirectResponse($this->router->generate($gotTo, $goToParams), Response::HTTP_SEE_OTHER);
        }

        try {
            $callback = [$repository, 'remove'];
            assert(is_callable($callback));

//            $repository->remove($entity, true);
            call_user_func_array($callback, [$entity, true]);
            $template = ['id' => $id, 'type' => 'text-bg-success', 'message' => $successMsg];
        } catch (Exception $exception) {
            $template = ['id' => $id, 'type' => 'text-bg-danger', 'message' => $exception->getMessage()];
        }

        if ($isAjax) {
            return new Response($this->environment->render("partials/_form_success.html.twig", $template));
        }

        // Para requests normales, podrías agregar flash message
        // $this->requestStack->getSession()->getFlashBag()->add($template['type'], $template['message']);

        return new RedirectResponse($this->router->generate($gotTo, $goToParams), Response::HTTP_SEE_OTHER);
    }

//    /**
//     * @param Request $request
//     * @param object $repository
//     * @param object $entity
//     * @param string $formType
//     * @param string $action
//     * @param string $successMsg
//     * @param string $gotTo
//     * @param string $templateDir
//     * @param array<mixed> $vars
//     * @param bool $modal
//     * @return Response
//     * @throws LoaderError
//     * @throws RuntimeError
//     * @throws SyntaxError
//     */
//    public function newAction(
//        Request $request,
//        object  $repository,
//        object  $entity,
//        string  $formType,
//        string  $action,
//        string  $successMsg,
//        string  $gotTo,
//        string  $templateDir,
//        array   $vars = [],
//        bool    $modal = false
//    ): Response
//    {
//        $form = $this->formFactory->create($formType, $entity, [
//            'action' => $action,
//        ]);
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
//
//            $repository->save($entity, true);
//
//            //si el formulario se mando correctamente por ajax
//            if ($request->isXmlHttpRequest()) {
//                $template = $this->environment->render("partials/_form_success.html.twig", [
//                    'id' => 'new_' . $this->getClassName($entity::class) . '_' . $entity->getId(),
//                    'type' => 'text-bg-success',
//                    'message' => $successMsg
//                ]);
//                return new Response($template);
//            }
//
//            $this->requestStack->getSession()->getFlashBag()->add('success', $successMsg);
//            return new RedirectResponse($this->router->generate($gotTo), Response::HTTP_SEE_OTHER);
//        }
//
//        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : (($modal) ? '_form.html.twig' : 'new.html.twig');//comportamiento por controlador
//        return new Response($this->environment->render("$templateDir/$template", [
//                $templateDir => $entity,
//                'form' => $form->createView(),
//            ] + $vars), ($form->isSubmitted() && !$form->isValid()) ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK);
//    }

//    /**
//     * @param Request $request
//     * @param object $repository
//     * @param object $entity
//     * @param string $formType
//     * @param string $action
//     * @param string $successMsg
//     * @param string $gotTo
//     * @param string $templateDir
//     * @param array<mixed> $vars
//     * @return Response
//     * @throws LoaderError
//     * @throws RuntimeError
//     * @throws SyntaxError
//     */
//    public function editAction(
//        Request $request,
//        object  $repository,
//        object  $entity,
//        string  $formType,
//        string  $action,
//        string  $successMsg,
//        string  $gotTo,
//        string  $templateDir,
//        array   $vars = []
//    ): Response
//    {
//        $form = $this->formFactory->create($formType, $entity, [
//            'action' => $action,
//        ]);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $repository->flush();
//
//            //si el formulario se mando correctamente por ajax
//            if ($request->isXmlHttpRequest()) {
//                $template = $this->environment->render("partials/_form_success.html.twig", [
//                    'id' => 'edit_' . $this->getClassName($entity::class) . '_' . $entity->getId(),
//                    'type' => 'text-bg-success',
//                    'message' => $successMsg
//                ]);
//                return new Response($template);
//            }
//
//            $this->requestStack->getSession()->getFlashBag()->add('success', $successMsg);
//            return new RedirectResponse($this->router->generate($gotTo), Response::HTTP_SEE_OTHER);
//        }
//
//        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : 'edit.html.twig';
//        return new Response($this->environment->render("$templateDir/$template", [
//                $templateDir => $entity,
//                'form' => $form->createView(),
//            ] + $vars), ($form->isSubmitted() && !$form->isValid()) ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK);
//    }

    /**
     * @param Request $request
     * @param object $entity
     * @param string $templateDir
     * @param array<mixed> $vars
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
        $callback = [$entity, 'getId'];
        assert(is_callable($callback));

        $template = ($request->isXmlHttpRequest()) ? '_form.html.twig' : (($modal) ? '_form.html.twig' : (call_user_func_array($callback, []) ? 'edit.html.twig' : 'new.html.twig'));//comportamiento por controlador
        return new Response($this->environment->render("$templateDir/$template", [
                $templateDir => $entity,
                'ajax' => $request->isXmlHttpRequest(),
//                'modal' => ($modal == false) ? $request->query->get('modal', '') : null
            ] + $vars));
    }

    /**
     * @param Request $request
     * @param object $entity
     * @param array<mixed> $entities
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function options(Request $request, object $entity, array $entities): Response
    {
        if ($request->isXmlHttpRequest()) {
            $callback = [$entity, 'getId'];
            assert(is_callable($callback));

            return new Response($this->environment->render('partials/_select_options.html.twig', [
                'entities' => $entities,
                'selected' => call_user_func_array($callback, [])
            ]));
        }

        throw new BadRequestHttpException('Ajax request');
    }

    /**
     * @param string $classname
     * @return false|int|string
     */
    private function getClassName(string $classname): false|int|string
    {
        if ($pos = strrpos($classname, '\\')) return substr($classname, $pos + 1);
        return $pos;
    }
}
