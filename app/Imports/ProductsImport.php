<?php

namespace App\Imports;


use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithDrawings;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;

use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

use App\Models\Product;
use App\Models\Product_image;
use Auth;

class ProductsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // dd($row);
        $product = Product::create([
            'product_name' => $row['name'],
            'description' => $row['description'],
            'side_effects' => $row['side_effect'],
            'producttypeid' => $row['type'],
            'quantity' => $row['quantity'],
            'price' => $row['price'],
            'offer_price' => $row['offer_price'],
            'manufacturer' => $row['manufacturer'],
            'storage' => $row['storage'],
            'added_by' => Auth::guard('admin')->user()->id,
            'vendor_type' => 'admin',
            'status' => 'active',
            'prescription' => ($row['prescription'] == 'yes' ? 1 : 0),
        ]);

        //Excel image insert code--

        $spreadsheet = IOFactory::load(request()->file('bulk_products'));
        $spc = $spreadsheet->getActiveSheet()->getDrawingCollection();

        $ProductImagerow = (array) $spc;
        // dd($ProductImagerow);

        $i = 0;
        foreach ($ProductImagerow as $drawing) {
            $zipReader = fopen($drawing->getPath(), 'r');
            $imageContents = '';
            while (!feof($zipReader)) {
                $imageContents .= fread($zipReader, 1024);
            }
            fclose($zipReader);
            $extension = $drawing->getExtension();

            $myFileName = time() .++$i. '.' . $extension;
            file_put_contents(public_path('/assets/uploads/products/') . $myFileName, $imageContents);

            $ImageID = Product_image::create([
                'product_id' => 113,//$product->id,
                'product_image' => $myFileName,
            ])->id;

            // Product::find($product->id)->update(['thumbnail'=>$ImageID]);
        }

        // return $product;

    }

}
