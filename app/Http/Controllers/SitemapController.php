<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $baseUrl = config('app.url', 'https://novosjersey.com');
        $now = now()->toAtomString();

        $products = Product::where('is_active', true)
            ->select('id', 'updated_at')
            ->get();

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{$baseUrl}</loc>
        <lastmod>{$now}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{$baseUrl}/katalog</loc>
        <lastmod>{$now}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{$baseUrl}/tentang-kami</loc>
        <lastmod>{$now}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>{$baseUrl}/panduan-ukuran</loc>
        <lastmod>{$now}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <url>
        <loc>{$baseUrl}/pesan</loc>
        <lastmod>{$now}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
XML;

        foreach ($products as $product) {
            $loc = $baseUrl . '/katalog/' . $product->id;
            $lastmod = $product->updated_at->toAtomString();
            $xml .= <<<XML

    <url>
        <loc>{$loc}</loc>
        <lastmod>{$lastmod}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
XML;
        }

        $xml .= "\n</urlset>";

        return response($xml, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
