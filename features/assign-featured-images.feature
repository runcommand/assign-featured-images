Feature: Assign featured images to posts that support thumbnails

  Scenario: Replace all featured images on posts by default
    Given a WP install

    When I run `wp media import 'http://wp-cli.org/behat-data/codeispoetry.png' --post_id=1 --porcelain`
    Then save STDOUT as {ATTACHMENT_ID}

    When I run `wp post meta update 1 _thumbnail_id {ATTACHMENT_ID}`
    Then STDOUT should contain:
      """
      Success:
      """

    When I run `wp assign-featured-images`
    Then STDOUT should be:
      """
      Found 2 posts to inspect for featured images.
      Post 1 has a valid featured image. Replacing with attachment {ATTACHMENT_ID}.
      Post 2 is missing a featured image. Replacing with attachment {ATTACHMENT_ID}.
      Success: Assigned featured images to 2 posts.
      """

  Scenario: `--only-missing` only affects posts with missing or invalid featured images
    Given a WP install

    When I run `wp media import 'http://wp-cli.org/behat-data/codeispoetry.png' --post_id=1 --porcelain`
    Then save STDOUT as {ATTACHMENT_ID}

    When I run `wp post meta update 1 _thumbnail_id {ATTACHMENT_ID}`
    Then STDOUT should contain:
      """
      Success:
      """

    When I run `wp post create --post_title="Foo" --porcelain`
    Then save STDOUT as {POST_ID}

    When I run `wp post meta update {POST_ID} _thumbnail_id 999999`
    Then STDOUT should contain:
      """
      Success:
      """

    When I run `wp assign-featured-images --only-missing`
    Then STDOUT should be:
      """
      Found 3 posts to inspect for featured images.
      Post 1 has a valid featured image. Skipping replacement.
      Post 2 is missing a featured image. Replacing with attachment {ATTACHMENT_ID}.
      Post {POST_ID} has an invalid featured image. Replacing with attachment {ATTACHMENT_ID}.
      Success: Assigned featured images to 2 posts.
      """

  Scenario: No posts to inspect
    Given a WP install
    And I run `wp site empty --yes`

    When I try `wp assign-featured-images`
    Then STDERR should be:
      """
      Error: No posts found.
      """
