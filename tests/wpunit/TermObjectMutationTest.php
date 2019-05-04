<?php

use GraphQLRelay\Relay;

// XXX: Can we autoload this somehow?
require_once __DIR__ . '/PolylangUnitTestCase.php';

class TermObjectMutationTest extends PolylangUnitTestCase
{
    public $admin_id = 1;

    static function wpSetUpBeforeClass()
    {
        parent::wpSetUpBeforeClass();

        self::set_default_language('en_US');
        self::create_language('en_US');
        self::create_language('fr_FR');
        self::create_language('fi');
        self::create_language('de_DE_formal');
        self::create_language('es_ES');
    }

    public function setUp()
    {
        parent::setUp();

        // XXX not enough permissions??
        // $this->admin = $this->factory->user->create( [
        // 	'role' => 'administrator',
        // ] );
    }

    public function testTermCreateWithLanguage()
    {
        wp_set_current_user($this->admin_id);

        $query = '
        mutation InsertTerm {
            createTag(input: {clientMutationId: "1", name:"testtag", language: FI}) {
              tag {
                name
                tagId
                language {
                  code
                }
              }
            }
          }
        ';

        $data = do_graphql_request($query);
        $this->assertArrayNotHasKey('errors', $data, print_r($data, true));
        $term_id = $data['data']['createTag']['tag']['tagId'];
        $lang = pll_get_term_language($term_id, 'slug');
        $this->assertEquals('fi', $lang);
    }
}