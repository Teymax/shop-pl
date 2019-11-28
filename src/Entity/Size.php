<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SizeRepository")
 */
class Size
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $height;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $width;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $addPrice;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product", mappedBy="sizes", cascade={"persist", "remove", "refresh"})
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductOrder", mappedBy="size")
     */
    private $productOrders;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->productOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(string $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(string $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAddPrice(): ?string
    {
        return $this->addPrice;
    }

    public function setAddPrice(?string $addPrice): self
    {
        $this->addPrice = $addPrice;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * Add product
     *
     * @param Product $product
     * @return Size
     */
    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addSize($this);
        }

        return $this;
    }

    /**
     * Remove product
     *
     * @param Product $product
     * @return Size
     */
    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            $product->removeSize($this);
        }

        return $this;
    }

    public function __toString()
    {
        return strval($this->getHeight()) . 'x' . strval($this->getWidth());
    }

    public function printSizeInfo()
    {
        $this->addPrice ? $addPrice = ' (+' . $this->getAddPrice() . ')' : $addPrice = '';
        $this->description ? $description = $this->description . ' ' : $description = $this->description;
        return $this->getHeight() . ' x ' . $this->getWidth() . ' ' . $description . $addPrice;
    }

    /**
     * @return Collection|ProductOrder[]
     */
    public function getProductOrders(): Collection
    {
        return $this->productOrders;
    }

    public function addProductOrder(ProductOrder $productOrder): self
    {
        if (!$this->productOrders->contains($productOrder)) {
            $this->productOrders[] = $productOrder;
            $productOrder->setSize($this);
        }

        return $this;
    }

    public function removeProductOrder(ProductOrder $productOrder): self
    {
        if ($this->productOrders->contains($productOrder)) {
            $this->productOrders->removeElement($productOrder);
            // set the owning side to null (unless already changed)
            if ($productOrder->getSize() === $this) {
                $productOrder->setSize(null);
            }
        }

        return $this;
    }
}
