<?php

namespace App\Controller;

use App\Cache\PromotionCache;
use App\DTO\LowestPriceEnquiry;
use App\Entity\Promotion;
use App\Filter\PromotionsFilterInterface;
use App\Repository\ProductRepository;
use App\Repository\PromotionRepository;
use App\Service\Serializer\DTOSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Filter\LowsetPriceFilter;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;


class ProductsController extends AbstractController
{

    public function __construct(private ProductRepository $repository, private EntityManagerInterface $entityManager)
    {

    }

    #[Route('/products/{id}/lowset-price', name: 'lowset_price', methods: 'POST')]
    public function lowestPrice(Request           $request,
                                int               $id,
                                DTOSerializer     $serializer,
                                LowsetPriceFilter $promotionsFilter,
                                PromotionCache    $promotionCache

    ): Response
    {
        if ($request->headers->has('force_fail')) {
            return new JsonResponse(
                ['error' => 'Promotion Engine failure message'],
                $request->headers->get('force_fail')
            );
        }
        /**  @var LowestPriceEnquiry $lowsetPriceEnquiry */
        $lowsetPriceEnquiry = $serializer->deserialize($request->getContent(), LowestPriceEnquiry::class, 'json');

        $product = $this->repository->find($id);
        $lowsetPriceEnquiry->setProduct($product);

        //Cache
        $promotions = $promotionCache->findValidForProduct($product, $lowsetPriceEnquiry->getRequestDate());

        $modifiedEnquiry = $promotionsFilter->apply($lowsetPriceEnquiry, ...$promotions);

        $contentReponse = $serializer->serialize($modifiedEnquiry, 'json');

        return new Response($contentReponse, 200, ['Content-Type' => 'application/json']);
    }


    #[Route('/products/{id}/promotions', name: 'promotions', methods: 'GET')]
    public function promotions()
    {

    }
}
