<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Squad;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_squad_model_uses_scout()
    {
        $this->assertTrue(in_array('Laravel\Scout\Searchable', class_uses(Squad::class)));
    }

    /**
     * Compares the searchable array with a hardcoded array
     *
     * @return void
     */
    public function test_squad_model_has_searchable_array()
    {
        $squad = Squad::factory()->create();

        $searchableArray = $squad->toSearchableArray();

        $this->assertEquals($squad->rankLabel(), $searchableArray['rank_label']);
        $this->assertEquals($squad->countryName(), $searchableArray['country_name']);
        $this->assertEquals($squad->nameWords(), $searchableArray['name_words']);
        $this->assertEquals($squad->rankValue(), $searchableArray['rank_value']);
    }
}
