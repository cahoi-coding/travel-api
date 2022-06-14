<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToursController extends Controller
{

    // they have no ratings inside, so I have to add them
    private function addRatingsToTour($tours)
    {
        for($x = 0; $x < count($tours); $x++)
        {
            $tours[$x] = (array)$tours[$x];
            $tours[$x]['ratings'] = $this->getRatings($tours[$x]['tour_id']);
            $tours[$x] = (object)$tours[$x];
        }

        return $tours;
    }

    //get all tours
    public function getTours(): array
    {
        $query = "
            SELECT t.* FROM travel.tours as t
        ";

        return $this->addRatingsToTour(DB::select($query));
    }

    //get ratings of a tour
    public function getRatings(int $id): array
    {
        $query = "
            SELECT * FROM travel.tour_ratings as tt
            WHERE tt.tour_id = $id
        ";
        return DB::select($query);
    }

    // search tours by a keyword
    public function searchTours(Request $request)
    {
        $keyword = $request->all()['keyword'];

        $keyword = "
            SELECT * FROM travel.tours as tt
            WHERE tt.tour_title LIKE '% ${keyword} %'
        ";

        return $this->addRatingsToTour(DB::select($keyword));
    }

    //get tours by conditions
    public function getConditionalTours(Request $request): array
    {
        $conditions = $request->all();

        $priceRange = $conditions['price_range'] ?? null;
        $priceCondition = $priceRange != null || $priceRange != "" || $priceRange != []
            ? "AND (CAST(t.tour_price AS DECIMAL(10,2)) BETWEEN ${priceRange['min']} AND ${priceRange['max']})"
            : "";

        $supplier = $conditions['supplier_id'] ?? null;
        $supplierCondition = $supplier != null || $supplier != ""
            ? "AND (t.supplier_id = ${supplier})"
            : "";

        $location = $conditions['location'] ?? null;
        $locationCondition = $location != null || $location != ""
            ? "AND (t.tour_location LIKE '%${location}%')"
            : "";

        $query = "
            SELECT t.* FROM travel.tours as t
            JOIN travel.suppliers s on s.supplier_id = t.supplier_id
            JOIN travel.tour_ratings tr on t.tour_id = tr.tour_id
            WHERE (TRUE $priceCondition $supplierCondition $locationCondition)
            GROUP BY t.tour_id
        ";

        return $this->addRatingsToTour(DB::select($query));
    }
}
