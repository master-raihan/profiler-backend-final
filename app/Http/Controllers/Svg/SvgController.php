<?php

namespace App\Http\Controllers\Svg;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use SVG\SVG;
use SVG\Nodes\Shapes\SVGRect;
use SVG\Nodes\Embedded\SVGImage;
use SVG\Nodes\Texts\SVGText;

class SvgController extends Controller
{

    public function svg(Request $request): \Illuminate\Http\JsonResponse
    {
        $image = SVG::fromFile('http://localhost/svg-server/icon.svg');
        $doc = $image->getDocument();

        $objects = $request->json()->all();

        foreach ($objects as $object){
            if($object['type'] == "rect") {
                $square = $this->renderRect($object);
                $doc->addChild($square);
            }

            if($object['type'] == "image") {
                $img = $this->renderImage($object);
                $doc->addChild($img);
            }

            if($object['type'] == "text") {
                $text = $this->renderText($object);
                $doc->addChild($text);
            }
        }

        $xmlString = $image->toXMLString();
        file_put_contents('my-image.svg', $xmlString);

        return response()->json(strval($image));
    }

    private function renderRect($object)
    {
        if ($object['xStart'] < $object['xEnd']) {
            $x = $object['xStart'];
            $w = $object['xEnd'] - $object['xStart'];
        } else {
            $x = $object['xEnd'];
            $w = $object['xStart'] - $object['xEnd'];
        }

        if ($object['yStart'] < $object['yEnd']) {
            $y = $object['yStart'];
            $h = $object['yEnd'] - $object['yStart'];
        } else {
            $y = $object['yEnd'];
            $h = $object['yStart'] - $object['yEnd'];
        }

        $square = new SVGRect($x, $y, $w, $h);
        $square->setStyle('fill', $object['bg']);

        return $square;
    }

    private function renderImage($object)
    {
        if ($object['xStart'] < $object['xEnd']) {
            $x = $object['xStart'];
            $w = $object['xEnd'] - $object['xStart'];
        } else {
            $x = $object['xEnd'];
            $w = $object['xStart'] - $object['xEnd'];
        }

        if ($object['yStart'] < $object['yEnd']) {
            $y = $object['yStart'];
            $h = $object['yEnd'] - $object['yStart'];
        } else {
            $y = $object['yEnd'];
            $h = $object['yStart'] - $object['yEnd'];
        }

        $image = new SVGImage($object['imagePath'],$x, $y, $w, $h);

        return $image;
    }

    private function renderText($object)
    {
        if ($object['xStart'] < $object['xEnd']) {
            $x = $object['xStart'];
        } else {
            $x = $object['xEnd'];
        }

        if ($object['yStart'] < $object['yEnd']) {
            $y = $object['yStart'];
        } else {
            $y = $object['yEnd'];
        }

        $text = new SVGText($object['text'], $x, $y);
        $text->setStyle('font-size', $object['fontSize']);
        $text->setStyle('fill', $object['fontColor']);

        return $text;
    }

}
