<?php


namespace App\Filter;

use App\DTO\PriceEnquiryInterface;
use App\DTO\PromotionEnquiryInterface;
use App\Filter\Modifier\Factory\PriceModifierFactoryInterface;

class LowsetPriceFilter implements PriceFilterInterface
{

    public function __construct(private PriceModifierFactoryInterface $priceModidierFactory)
    {

    }


    public function apply(PriceEnquiryInterface $enquiry, ...$promotions): PriceEnquiryInterface
    {

        $price = $enquiry->getProduct()->getPrice();
        $enquiry->setPrice($price);

        $quantity = $enquiry->getQuantity();

        $lowsetPrice = $quantity * $price;

        foreach ($promotions as $promotion) {
            $priceModified = $this->priceModidierFactory->create($promotion->getType());
            $modifiedPrice = $priceModified->modify($price, $quantity, $promotion, $enquiry);
            if ($modifiedPrice < $lowsetPrice) {
                $enquiry->setDiscountedPrice($modifiedPrice);
                $enquiry->setPromotionId($promotion->getId());
                $enquiry->setPromotionName($promotion->getName());

                $lowsetPrice = $modifiedPrice;
            }
        }

        return $enquiry;

    }

}
