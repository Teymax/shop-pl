<?php

namespace App\Entity;

use App\Entity\Product;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

/**
 * ProductImage
 *
 * @ORM\Table(name="product_image")
 * @ORM\Entity(repositoryClass="App\Repository\ProductImageRepository")
 * @Vich\Uploadable
 */
class ProductImage
{
    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="images", cascade={"remove"})
     */
    private $product;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     * @var string
     */
    private $image;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="product_image", fileNameProperty="image")
     */
    private $imageFile;

    /**
     * @ORM\Column(name="is_main", type="integer", length=1, nullable=true)
     */
    private $isMain;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set product
     *
     * @param Product $product
     *
     * @return ProductImage
     */
    public function setProduct(Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param File|null $image
     * @return ProductImage
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        return $this;
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param string $image
     * @return ProductImage
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    public function __toString()
    {
        return (string)$this->image;
    }


    public function getIsMain() :bool
    {
        return $this->isMain?$this->isMain:false;
    }


    public function setIsMain($isMain)
    {
        $this->isMain = $isMain;

        return $this;
    }

}