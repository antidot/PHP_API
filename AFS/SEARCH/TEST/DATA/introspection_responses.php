<?php
/**
 * Created by PhpStorm.
 * User: ct
 * Date: 11/18/15
 * Time: 2:03 PM
 */

define('RESULT_WITHOUT_FACETS', '{
  "header": {
    "query": {
      "userId": "249716644",
      "userId": "368eca82-7270-4770-8751-860d57e4a696",
      "date": "2015-11-11T12:00:12+0100",
      "queryParam": [
        {
          "name": "afs:service",
          "value": "7123"
        },
        {
          "name": "afs:status",
          "value": "stable"
        },
        {
          "name": "afs:what",
          "value": "meta"
        },
        {
          "name": "afs:output",
          "value": "json,2"
        },
        {
          "name": "afs:facetDefault",
          "value": "replies=100"
        },
        {
          "name": "afs:replies",
          "value": "20"
        },
        {
          "name": "afs:sort",
          "value": "afs:relevance,DESC;category_level,ASC;category_broader_length,ASC;category_name_length,ASC"
        }
      ],
      "mainCtx": {
        "textquery": ""
      },
      "textquery": ""
    },
    "metadata": [
      {
        "uri": "Catalog",
        "meta": {
          "producer": [],
          "info": {
            "sizeKb": 409093,
            "date": 1447110000,
            "searchFeedInfo": {
              "nbDocs": 33210,
              "nbShards": 1,
              "setInfos": []
            }
          }
        }
      }
    ]
  }
}');

define('RESULT_WITH_FACETS_FLAT', '{
  "header": {
    "query": {
      "userId": "249716644",
      "userId": "368eca82-7270-4770-8751-860d57e4a696",
      "date": "2015-11-11T12:00:12+0100",
      "queryParam": [
        {
          "name": "afs:service",
          "value": "7123"
        },
        {
          "name": "afs:status",
          "value": "stable"
        },
        {
          "name": "afs:what",
          "value": "meta"
        },
        {
          "name": "afs:output",
          "value": "json,2"
        },
        {
          "name": "afs:facetDefault",
          "value": "replies=100"
        },
        {
          "name": "afs:replies",
          "value": "20"
        },
        {
          "name": "afs:sort",
          "value": "afs:relevance,DESC;category_level,ASC;category_broader_length,ASC;category_name_length,ASC"
        }
      ],
      "mainCtx": {
        "textquery": ""
      },
      "textquery": ""
    }
  },
  "metadata": [
    {
      "uri": "Catalog",
      "meta": {
        "producer": [],
        "info": {
          "sizeKb": 409093,
          "date": 1447110000,
          "searchFeedInfo": {
            "nbDocs": 33210,
            "nbShards": 1,
            "setInfos": [
              {
                "setId": "Antidot_Root_Field",
                "childrenInfos": [],
                "facetInfos": [
                  {
                    "afs:t": "FacetTree",
                    "layout": "TREE",
                    "type": "INTEGER",
                    "id": "product_id",
                    "sticky": false,
                    "filter": true
                  },
                  {
                    "afs:t": "FacetTree",
                    "layout": "TREE",
                    "type": "STRING",
                    "id": "name",
                    "sticky": false,
                    "filter": true
                  },
                  {
                    "afs:t": "FacetTree",
                    "layout": "TREE",
                    "type": "REAL",
                    "id": "price_from",
                    "sticky": false,
                    "filter": true
                  }
                ]
              }
            ]
          }
        }
      }
    }
  ]
}');

define('RESULT_WITH_FACETS_MULTILEVEL', '{
  "header": {
    "query": {
      "userId": "249716644",
      "userId": "368eca82-7270-4770-8751-860d57e4a696",
      "date": "2015-11-11T12:00:12+0100",
      "queryParam": [
        {
          "name": "afs:service",
          "value": "7123"
        },
        {
          "name": "afs:status",
          "value": "stable"
        },
        {
          "name": "afs:what",
          "value": "meta"
        },
        {
          "name": "afs:output",
          "value": "json,2"
        },
        {
          "name": "afs:facetDefault",
          "value": "replies=100"
        },
        {
          "name": "afs:replies",
          "value": "20"
        },
        {
          "name": "afs:sort",
          "value": "afs:relevance,DESC;category_level,ASC;category_broader_length,ASC;category_name_length,ASC"
        }
      ],
      "mainCtx": {
        "textquery": ""
      },
      "textquery": ""
    }
  },
  "metadata": [
    {
      "uri": "Catalog",
      "meta": {
        "producer": [],
        "info": {
          "sizeKb": 409093,
          "date": 1447110000,
          "searchFeedInfo": {
            "nbDocs": 33210,
            "nbShards": 1,
            "setInfos": [
              {
                "setId": "Antidot_Root_Field",
                "childrenInfos": [
                  {
                    "setId": "variant",
                    "childrenInfos": [],
                    "facetInfos": [
                      {
                        "afs:t": "FacetTree",
                        "layout": "TREE",
                        "type": "STRING",
                        "id": "product",
                        "sticky": false,
                        "filter": true
                      },
                      {
                        "afs:t": "FacetTree",
                        "layout": "TREE",
                        "type": "STRING",
                        "id": "model",
                        "labels": [
                          {
                            "lang": "FR",
                            "label": "Modèle"
                          },
                          {
                            "lang": "NL",
                            "label": "model"
                          }
                        ],
                        "sticky": false,
                        "filter": false
                      }
                    ]
                  }
                ],
                "facetInfos": [
                  {
                    "afs:t": "FacetTree",
                    "layout": "TREE",
                    "type": "INTEGER",
                    "id": "product_id",
                    "sticky": false,
                    "filter": true
                  },
                  {
                    "afs:t": "FacetTree",
                    "layout": "TREE",
                    "type": "STRING",
                    "id": "name",
                    "sticky": false,
                    "filter": true
                  },
                  {
                    "afs:t": "FacetTree",
                    "layout": "TREE",
                    "type": "REAL",
                    "id": "price_from",
                    "sticky": false,
                    "filter": true
                  }
                ]
              }
            ]
          }
        }
      }
    }
  ]
}');

define('RESPONSE_FACETS_MULTIFEED', '{
  "header": {
    "query": {
      "userId": "249716644",
      "userId": "368eca82-7270-4770-8751-860d57e4a696",
      "date": "2015-11-11T12:00:12+0100",
      "queryParam": [
        {
          "name": "afs:service",
          "value": "7123"
        },
        {
          "name": "afs:status",
          "value": "stable"
        },
        {
          "name": "afs:what",
          "value": "meta"
        },
        {
          "name": "afs:output",
          "value": "json,2"
        },
        {
          "name": "afs:facetDefault",
          "value": "replies=100"
        },
        {
          "name": "afs:replies",
          "value": "20"
        },
        {
          "name": "afs:sort",
          "value": "afs:relevance,DESC;category_level,ASC;category_broader_length,ASC;category_name_length,ASC"
        }
      ],
      "mainCtx": {
        "textquery": ""
      },
      "textquery": ""
    }
  },
  "metadata": [
    {
      "uri": "Catalog",
      "meta": {
        "producer": [],
        "info": {
          "sizeKb": 409093,
          "date": 1447110000,
          "searchFeedInfo": {
            "nbDocs": 33210,
            "nbShards": 1,
            "setInfos": [
              {
                "setId": "Antidot_Root_Field",
                "childrenInfos": [
                  {
                    "setId": "variant",
                    "childrenInfos": [],
                    "facetInfos": [
                      {
                        "afs:t": "FacetTree",
                        "layout": "TREE",
                        "type": "STRING",
                        "id": "product",
                        "sticky": false,
                        "filter": true
                      },
                      {
                        "afs:t": "FacetTree",
                        "layout": "TREE",
                        "type": "STRING",
                        "id": "model",
                        "labels": [
                          {
                            "lang": "FR",
                            "label": "Modèle"
                          },
                          {
                            "lang": "NL",
                            "label": "model"
                          }
                        ],
                        "sticky": false,
                        "filter": false
                      }
                    ]
                  }
                ],
                "facetInfos": [
                  {
                    "afs:t": "FacetTree",
                    "layout": "TREE",
                    "type": "INTEGER",
                    "id": "product_id",
                    "sticky": false,
                    "filter": true
                  },
                  {
                    "afs:t": "FacetTree",
                    "layout": "TREE",
                    "type": "STRING",
                    "id": "name",
                    "sticky": false,
                    "filter": true
                  },
                  {
                    "afs:t": "FacetTree",
                    "layout": "TREE",
                    "type": "REAL",
                    "id": "price_from",
                    "sticky": false,
                    "filter": true
                  }
                ]
              }
            ]
          }
        }
      }
    },
    {
      "uri": "Categories",
      "meta": {
        "producer": [],
        "info": {
          "sizeKb": 409093,
          "date": 1447110000,
          "searchFeedInfo": {
            "nbDocs": 33210,
            "nbShards": 1,
            "setInfos": [
              {
                "setId": "Antidot_Root_Field",
                "childrenInfos": [],
                "facetInfos": [
                  {
                    "afs:t": "FacetTree",
                    "layout": "TREE",
                    "type": "INTEGER",
                    "id": "category_id",
                    "sticky": false,
                    "filter": true
                  }
                ]
              }
            ]
          }
        }
      }
    }
  ]
}');

define('ABOUT_RESPONSE', '{
  "x:type":"ws.response",
  "query":{
    "x:type":"ws.response.query",
    "parameters":{
      "x:type":"collection",
      "x:values":[

      ]
    },
    "properties":{
      "x:type":"x:dynamic"
    }
  },
  "result":{
    "x:type":"bows.about",
    "boWsVersion":{
      "x:type":"AfsVersion",
      "build":"3eaebfd1f1fe261780347cbc35bfbd65d613575e",
      "gen":"7.6",
      "major":"4",
      "minor":"0",
      "motto":"Pink Dolphin"
    },
    "copyright":"Copyright (C) 1999-2013 Antidot"
  }
}');
