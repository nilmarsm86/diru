<?php

namespace App\DTO;

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Object paginator for table
 */
class Paginator
{
    /**
     * @param DoctrinePaginator<mixed>|array<mixed> $data
     * @param int $amount
     * @param int $page
     * @param int|null $fake
     */
    public function __construct(
        private readonly DoctrinePaginator|array $data = [],
        private int                              $amount = 10,
        private int                              $page = 1,
        private ?int                             $fake = null
    )
    {
    }

    /**
     * @param int $fake
     * @return $this
     */
    public function setFake(int $fake): static
    {
        $this->fake = $fake;
        return $this;
    }

    /**
     * @param int $amount
     * @return $this
     */
    public function setAmount(int $amount): static
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @param int $page
     * @return $this
     */
    public function setPage(int $page): static
    {
        $this->page = $page;
        return $this;
    }

    /**
     * Get data to paginate
     * @return DoctrinePaginator<mixed>|array<mixed>
     */
    public function getData(): DoctrinePaginator|array
    {
        return $this->data;
    }

    /**
     * Max page amount inpagination
     * @return int|float
     */
    public function getMaxPage(): int|float
    {
        return ceil($this->getTotal() / $this->amount);
    }

    /**
     * Start item of the page
     * @return int
     */
    public function from(): int
    {
        //arreglar bug cuando se pone una cantidad a mostrar mayor que la que hay y esta fuera de rango la pagina
        if ($this->getTotal() === 0) {
            return 0;
        }
        return ($this->page * $this->amount) - $this->amount + 1;
    }

    /**
     * End item of the page
     * @return int
     */
    public function to(): int
    {
        $total = $this->getTotal();
        return (($this->page * $this->amount) < $total) ? $this->page * $this->amount : $total;
    }

    /**
     * Total items
     * @return int
     */
    public function getTotal(): int
    {
        if (is_null($this->fake)) {
//            if(is_array($this->data)){
//                return count($this->data);
//            }else{
//                return $this->data->count();
//            }
            return (is_array($this->data)) ? count($this->data) : $this->data->count();
        } else {
            return $this->fake;
        }

    }

    /**
     * Amount for page
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * Numer of the current page
     * @return int
     */
    public function currentPage(): int
    {
        return $this->page;
    }

    /**
     * If not a valid page number
     * @return bool
     */
    public function isFromGreaterThanTotal(): bool
    {
        return $this->from() > $this->getTotal();
    }

    /**
     * @param Request $request
     * @param RouterInterface $router
     * @param int $pageNumber
     * @return RedirectResponse
     */
    public function greatherThanTotal(Request $request, RouterInterface $router, int $pageNumber)
    {
        $number = ($pageNumber === 1) ? 1 : ($pageNumber - 1);
        $route = $request->attributes->get('_route');
        return new RedirectResponse($router->generate(is_string($route) ? $route : '', [
            ...$request->query->all(),
            'page' => $number
        ]), Response::HTTP_SEE_OTHER);
    }

}
