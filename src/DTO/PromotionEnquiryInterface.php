<?php

namespace App\DTO;

//interface PromotionEnquiryInterface extends \JsonSerializable
use App\Entity\Product;

interface PromotionEnquiryInterface
{
    public function getProduct(): ?Product;

    public function setPromotionId(int $promotionId);

    public function setPromotionName(string $promotionName);


}