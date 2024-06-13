<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Searchable Attributes
    |--------------------------------------------------------------------------
    |
    | Limits the scope of a search to the attributes listed in this setting. Defining
    | specific attributes as searchable is critical for relevance because it gives
    | you direct control over what information the search engine should look at.
    |
    | Supported: Null, Array
    | Example: ["name", "email", "unordered(city)"]
    |
    */

    'searchableAttributes' => ['unordered(name)', 'unordered(name_words)', 'code', 'unordered(description)'],

    /*
    |--------------------------------------------------------------------------
    | Remove Stop Words
    |--------------------------------------------------------------------------
    |
    | Stop word removal is useful when you have a query in natural language, e.g.
    | “what is a record?”. In that case, the engine will remove “what”, “is”,
    | before executing the query, and therefore just search for “record”.
    |
    | Supported: Null, Boolean, Array
    |
    */

    'removeStopWords' => null,

    /*
    |--------------------------------------------------------------------------
    | Disable Typo Tolerance
    |--------------------------------------------------------------------------
    |
    | Algolia provides robust "typo-tolerance" out-of-the-box. This parameter accepts an
    | array of attributes for which typo-tolerance should be disabled. This is useful,
    | for example, products that might require SKU search without "typo-tolerance".
    |
    | Supported: Null, Array
    | Example: ['id', 'sku', 'reference', 'code']
    |
    */

    'disableTypoToleranceOnAttributes' => ['code'],

    /*
    |--------------------------------------------------------------------------
    | Disable Exact On Attributes
    |--------------------------------------------------------------------------
    |
    | List of attributes on which you want to disable the exact ranking criterion
    |
    |
    */

    'disableTypoToleranceOnAttributes' => ['name', 'description'],

    /*
    |--------------------------------------------------------------------------
    | Attributes for Faceting
    |--------------------------------------------------------------------------
    |
    | The complete list of attributes that will be used for faceting.
    |
    | Use this setting for 2 reasons:
    |    to turn an attribute into a facet
    |    to make any string attribute filterable.
    | By default, your index comes with no categories. By designating an attribute as a facet, this enables Algolia to compute a set of possible values that can later be used to filter results or display these categories. You can also get a count of records that match those values.
    |
    */

    'attributesForFaceting' => [
        'searchable(country_name)',
        'rank_label',
        'requires_approval',
        'verified',
        'featured',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ranking
    |--------------------------------------------------------------------------
    |
    | Controls how Algolia should sort your results.
    |
    | You must specify a list of ranking criteria. The tie-breaking algorithm applies each criterion sequentially in the order they’re specified.
    |
    */

    'ranking' => ['desc(created_at)'],

    /*
    |--------------------------------------------------------------------------
    | Replicas
    |--------------------------------------------------------------------------
    |
    | Creates replicas (copies) of an index.
    |
    | You must specify a list of ranking criteria. The tie-breaking algorithm applies each criterion sequentially in the order they’re specified.
    |
    */

    'replicas' => [
        'squads_oldest',
        'squads_name_asc',
        'squads_name_desc',
        'squads_rank_asc',
        'squads_rank_desc',
        'squads_active_members_asc',
        'squads_active_members_desc',
    ],

    /*
    |--------------------------------------------------------------------------
    | Unretrievable Attributes
    |--------------------------------------------------------------------------
    |
    | This is particularly important for security or business reasons, where some attributes are
    | used only for ranking or other technical purposes, but should never be seen by your end
    | users, such as: total_sales, permissions, stock_count, and other private information.
    |
    | Supported: Null, Array
    | Example: ['total_sales', 'permissions', 'stock_count',]
    |
    */

    'unretrievableAttributes' => null,

    /*
    |--------------------------------------------------------------------------
    | Ignore Plurals
    |--------------------------------------------------------------------------
    |
    | Treats singular, plurals, and other forms of declensions as matching terms. When
    | enabled, will make the engine consider “car” and “cars”, or “foot” and “feet”,
    | equivalent. This is used in conjunction with the "queryLanguages" setting.
    |
    | Supported: Null, Boolean, Array
    |
    */

    'ignorePlurals' => null,

    /*
    |--------------------------------------------------------------------------
    | Query Languages
    |--------------------------------------------------------------------------
    |
    | Sets the languages to be used by language-specific settings such as
    | "removeStopWords" or "ignorePlurals". For optimum relevance, it is
    | recommended to only enable languages that are used in your data.
    |
    | Supported: Null, Array
    | Example: ['en', 'fr',]
    |
    */

    'queryLanguages' => ['en'],

    /*
    |--------------------------------------------------------------------------
    | Distinct
    |--------------------------------------------------------------------------
    |
    | Using this attribute, you can limit the number of returned records that contain the same
    | value in that attribute. For example, if the distinct attribute is the series_name and
    | several hits (Episodes) have the same value for series_name (Laravel From Scratch).
    |
    | Supported(distinct): Boolean
    | Supported(attributeForDistinct): Null, String
    | Example(attributeForDistinct): 'slug'
    */

    'distinct' => null,
    'attributeForDistinct' => null,

    /*
    |--------------------------------------------------------------------------
    | Other Settings
    |--------------------------------------------------------------------------
    |
    | The easiest way to manage your settings is usually to go to your Algolia dashboard because
    | it has a nice UI and you can test the relevancy directly there. Once you fine-tuned your
    | configuration, just use the command `scout:sync` to get remote settings in this file.
    |
    */

];
