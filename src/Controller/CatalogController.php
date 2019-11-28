<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Size;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CatalogController extends AbstractController
{
    /**
     * @Route("/catalog", name="catalog")
     */
    public function index()
    {
        $products = $this->getDoctrine()->getManager()->getRepository(Product::class)->findInStock();
        return $this->render('catalog/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/pdp/{product}", name="pdp")
     * @param Product $product
     * @return Response
     */
    public function pdp(Product $product)
    {
        return $this->render('catalog/pdp.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/addToCart/{product}/{quantity}/{size}", name="add_to_cart")
     * @param Request $request
     * @param Product $product
     * @param int $quantity
     * @param Size $size
     * @return Response
     */
    public function addToCart(Request $request, Product $product, int $quantity, Size $size)
    {
        if ($quantity < 1) return $this->redirectToRoute("remove_from_cart", ["product" => $product->getId()]);
        if ($quantity > $product->getInStock()) {
            $response = new Response(
                "we have only " . $product->getInStock() . " products",
                Response::HTTP_RESERVED,
                ['content-type' => 'text/plain']
            );
            return $response;
        }
        $newProduct = ['product' => $product->getId(), 'quantity' => $quantity, 'size' => $size->getId()];
        $products = json_decode($request->cookies->get("products"), true);
        $products = $products ? $products : [];
        $isUpdated = false;
        $updatedProducts = array_map(function ($productArr) use ($product, $size, $quantity, &$isUpdated) {
            if ($productArr["product"] == $product->getID() && $productArr["size"] == $size->getID()) {
                $productArr["quantity"] = intval($productArr['quantity']) + $quantity;
                $isUpdated = true;
            }
            return $productArr;
        }, $products);
        if (!$isUpdated) array_push($updatedProducts, $newProduct);
        $response = new Response(
            json_encode($updatedProducts),
            Response::HTTP_OK,
            ['content-type' => 'text/javascript']
        );
        $response->headers->setCookie(Cookie::create("products", json_encode($updatedProducts)));
        $response->sendHeaders();
        return $response;
    }

    /**
     * @Route("/removeFromCart", name="remove_from_cart")
     * @param Request $request
     * @return Response
     */
    public function removeFromCart(Request $request)
    {
        $product = $request->query->get("product");
        $response = new Response(
            $product,
            Response::HTTP_OK,
            ['content-type' => 'text/javascript']
        );
        $products = json_decode($request->cookies->get("products"), true);
        if (!$products || !isset($products[$product])) throw $this->createNotFoundException('The product does not exist');
        unset($products[$product]);
        $response->headers->setCookie(Cookie::create("products", json_encode($products)));
        $response->sendHeaders();
        return $response;
    }

    /**
     * @Route("/cart", name="cart")
     * @param Request $request
     * @return Response
     */
    public function cart(Request $request)
    {
        $productsArray = json_decode($request->cookies->get("products"), true);
        $products = is_null($productsArray) ? [] :
            $this->getDoctrine()->getManager()
                ->getRepository(Product::class)->findBy(["id" => array_map(function ($array) {
                    return $array['product'];
                }, $productsArray)]);
        /* @var Size[] $sizes */
        $sizes = is_null($productsArray) ? [] :
            $this->getDoctrine()->getManager()
                ->getRepository(Size::class)->findBy(["id" => array_map(function ($array) {
                    return $array['size'];
                }, $productsArray)]);
        $productsArray = $productsArray ? $productsArray : [];
        foreach ($productsArray as &$productArr) {
            $sizeEntity = array_filter($sizes, function ($size) use ($productArr) {
                return $size->getId() == $productArr["size"];
            });
            $productArr["size"] = array_pop($sizeEntity);
            $entityProduct = array_filter($products, function ($product) use ($productArr) {
                return $product->getId() == $productArr["product"];
            });

            $productArr["product"] = array_pop($entityProduct);
        }

        $template = $request->query->get("onlyItems") ? 'catalog/cartItems.html.twig' : 'catalog/cart.html.twig';
        return $this->render($template, [
            'products' => $productsArray,
        ]);
    }

    static function getSizes($array)
    {
        return $array['size'];
    }

}
