Feature: RestContext
  Scenario: Send json request with body
    Given I send a JSON GET request to 'route("homepage")' with body:
    """
    {
      "data": "Foo Bar"
    }
    """
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON should be a superset of:
    """
    {
      "data": "Foo Bar",
      "foo": "trans('foo')",
      "hello": "trans('hello')"
    }
    """

  Scenario: Send json request without body
    Given I send a JSON GET request to 'route("homepage")'
    Then the response status code should be 200
    And the response should be in JSON
    And the JSON should be a superset of:
    """
    {
      "foo": "Bar",
      "hello": "World"
    }
    """

