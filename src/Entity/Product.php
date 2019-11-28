<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $color;

    /**
     * @ORM\Column(type="integer")
     */
    private $inStock;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductOrder", mappedBy="product")
     */
    private $productOrders;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Size", inversedBy="products", cascade={"persist", "remove", "refresh"})
     */
    private $sizes;

    /**
     * @var ProductImage[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ProductImage", mappedBy="product", cascade={"persist"})
     */
    private $images;

    public function __construct()
    {
        $this->productOrders = new ArrayCollection();
        $this->sizes = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getInStock(): ?int
    {
        return $this->inStock;
    }

    public function setInStock(int $inStock): self
    {
        $this->inStock = $inStock;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
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
            $productOrder->setProduct($this);
        }

        return $this;
    }

    public function removeProductOrder(ProductOrder $productOrder): self
    {
        if ($this->productOrders->contains($productOrder)) {
            $this->productOrders->removeElement($productOrder);
            // set the owning side to null (unless already changed)
            if ($productOrder->getProduct() === $this) {
                $productOrder->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Size[]
     */
    public function getSizes(): Collection
    {
        return $this->sizes;
    }


    /**
     * Add size
     *
     * @param Size $size
     * @return Product
     */
    public function addSize(Size $size): self
    {
        if (!$this->getSizes()->contains($size)) {
            $this->sizes[] = $size;
        }

        return $this;
    }

    /**
     * Remove size
     *
     * @param Size $size
     * @return Product
     */
    public function removeSize(Size $size): self
    {
        if ($this->getSizes()->contains($size)) {
            $this->getSizes()->removeElement($size);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Get images
     *
     * @return ProductImage[]|ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Get main image
     *
     * @return ProductImage
     */
    public function getMainImage()
    {
        return $this->getImages()->filter(function (ProductImage $image) {
            return $image->getIsMain();
        })->first();
    }

    /**
     * Add image
     *
     * @param ProductImage $image
     *
     * @return Product
     */
    public function addImage(ProductImage $image)
    {
        $image->setProduct($this);
        $this->images[] = $image;

        dump($image);

        return $this;
    }

    /**
     * Remove image
     *
     * @param ProductImage $image
     */
    public function removeImage(ProductImage $image)
    {
        $image->setProduct(null);
        $this->images->removeElement($image);
    }
}
