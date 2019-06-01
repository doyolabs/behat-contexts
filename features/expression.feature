Feature: Test
  Scenario: It should find hello
    When I say hello
    Then output should be "world"
    When I say foo
    Then output should be "bar"

  Scenario: Test translation
    Given I have text:
    """
    Foo: trans("foo")
    Hello: trans("hello")
    """
    Then translated output should be:
    """
    Foo: Bar
    Hello: World
    """

  Scenario: Test route
    Given I have route 'route("homepage")'
    Then generated route should be "/"
    Given I have route 'route("foo")'
    Then generated route should be '/foo'
