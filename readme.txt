=== Social Media Auto Poster - Schedule & Publish to Postiz ===
Contributors: n7studios,wpzinc
Donate link: https://www.wpzinc.com/documentation/postiz-auto-poster
Tags: social media automation, auto post, postiz, social media scheduler, auto publish
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Automatically post and schedule your WordPress content to Facebook, X/Twitter, LinkedIn, Threads, Bluesky, and more social networks using Postiz.

== Description ==

Social Media Auto Poster connects your WordPress site to Postiz, enabling automatic social media publishing whenever you create or update content. Share your blog posts, pages, and custom post types to multiple social networks without manual posting.

=== Why Choose This Social Media Automation Plugin? ===

This plugin eliminates repetitive social media posting by automatically adding your WordPress content to your Postiz queue. Postiz then intelligently schedules posts to your connected social networks at optimal times for maximum engagement.

**Automatic Social Media Publishing** - Set it once and your content automatically shares to social media when published or updated.

**Smart Scheduling with Postiz** - Postiz's queue system prevents spam by spacing out posts according to your schedule. Your social media profiles stay active without overwhelming followers.

**Compatible Social Networks** - Auto post to Facebook Pages, Twitter (X), LinkedIn Pages and Profiles, Threads, Google Business Profile, Mastodon, Bluesky, and TikTok.

**Dynamic Content Templates** - Customize each social media post using template tags that pull your post title, excerpt, content, featured image, categories, tags, and custom fields.

Don't have a Postiz account? [Sign up for free](https://postiz.pro/wpzinc)

=== How to Auto Post to Social Media with Postiz ===

1. **Connect Your Postiz Account** - Simple one-click authorization, no API keys or technical setup required
2. **Link Your Social Networks** - Connect Facebook, X/Twitter, LinkedIn, and other profiles through Postiz's interface
3. **Configure Post Settings** - Choose which post types to share and customize your social media message templates
4. **Publish Content** - Your WordPress posts automatically share to social media according to your Postiz schedule

=== Social Media Networks Supported ===

**Facebook Auto Posting** - Share to Facebook Pages automatically when you publish WordPress content.

**X/Twitter Auto Posting** - Auto post to Twitter (X) reliably.

**LinkedIn Auto Posting** - Publish to LinkedIn Company Pages and personal LinkedIn Profiles to grow your professional network.

**Additional Networks** - Threads, Google Business Profile, Mastodon, Bluesky, and TikTok support included.

=== Dynamic Template Tags for Customized Posts ===

Create unique social media messages using template tags:

* **{title}** - Your post title
* **{excerpt}** - Post excerpt (with character/word limits)
* **{content}** - Post content (with character/word limits)
* **{url}** - Post permalink
* **{date}** - Publication date
* **{taxonomy_post_tag}** - Tags as hashtags
* **{taxoomy_category}** - Categories as hashtags

=== Better Than Traditional Auto Posting Plugins ===

Unlike direct posting plugins (WP to Facebook, WP to Twitter clones), this plugin uses Postiz's smart queue system. Benefits include:

**Prevents Social Media Penalties** - Postiz spaces posts naturally, avoiding spam flags from posting too frequently
**Optimized Timing** - Schedule posts for when your audience is most active
**Cross-Network Management** - Manage all social networks from one Postiz dashboard
**No API Complications** - Postiz handles all social network API connections and changes
**Duplicate Prevention** - Built-in protection ensures you never post the same content twice

=== How to Schedule Social Media Posts ===

**Default Postiz Schedule** - Postiz automatically spaces posts throughout the day based on your time zone preferences

**Custom Posting Schedule** (Pro) - Define specific days and times in Postiz when posts should publish to each social network

**Immediate Posting** (Pro) - Override the queue and post immediately to social media

**Scheduled Publishing** (Pro) - Set exact date and time for each social media post

=== Support for Free Version ===

We provide community support through the <a href="https://wordpress.org/support/plugin/postiz-auto-poster/">WordPress support forums</a>.

=== Privacy and Data Usage ===

Our [API](https://www.wpzinc.com/documentation/wordpress-postiz/data/) connects your website to [Postiz](https://postiz.pro/wpzinc). An account with Postiz is required.

We connect directly to your Postiz (postiz.io) account, via their API, to:
- Fetch your social media profile names and IDs, 
- Send your WordPress Posts to one or more of your social media profiles.  The profiles and content sent will depend on the plugin settings you have configured.

We connect to our own [API](https://www.wpzinc.com/documentation/wordpress-postiz-pro/data/) to pass the following requests through to Postiz:
- Connect our Plugin to Postiz, when you click the Authorize button (this obtains an access token from Postiz, once you have approved authorization)

Both of these are done via our own API, to ensure that no secret data (such as oAuth client secret keys) are included in this Plugin's code or made public.

We **never** store any information on our web site or API during this process.

== Installation ==

= Automatic Installation (Recommended) =

1. Log in to your WordPress admin dashboard
2. Navigate to Plugins > Add New
3. Search for "Postiz Auto Poster"
4. Click "Install Now" on the Postiz Auto Poster plugin
5. Click "Activate" once installation completes
6. Go to Postiz Auto Poster in your admin menu
7. Click "Authorize" to connect your Postiz account
8. Configure your social media posting settings

= Manual Installation =

1. Download the plugin ZIP file
2. Log in to your WordPress admin dashboard
3. Navigate to Plugins > Add New > Upload Plugin
4. Choose the downloaded ZIP file and click "Install Now"
5. Click "Activate Plugin"
6. Go to Postiz Auto Poster in your admin menu
7. Click "Authorize" to connect your Postiz account
8. Configure your social media posting settings

== Frequently Asked Questions ==

= Which social media platforms are supported for auto posting? =

* Facebook Pages
* Twitter (X)
* LinkedIn Pages and Profiles  
* Threads
* Google Business Profile
* Mastodon
* Bluesky
* TikTok

= Do I need a paid Postiz account? =

Yes. Postiz doesn't offer a free plan, but you can try their 7 day free trial first.

= How does this differ from other auto posting plugins? =

Unlike plugins that post directly to social networks, we integrate with Postiz's smart scheduling system. This provides several advantages:

**Spam Prevention** - Postiz spaces posts naturally to avoid social media penalties
**Flexible Scheduling** - Control when posts publish with Postiz's schedule settings
**API Reliability** - Postiz handles all social network API complexities and updates
**Cross-Platform Management** - Manage all networks from Postiz's unified dashboard

= Can I customize the social media message for each post? =

Yes. Use template tags to dynamically build messages:

* {title} - Post title
* {excerpt} - Post excerpt
* {content} - Post content
* {url} - Post URL
* {date} - Publication date
* {author} - Author name
* {tags} - Post tags as hashtags
* {categories} - Categories as hashtags

Pro version allows different templates per social network and per post type.

= Can I schedule posts to publish at specific times? =

Posts are added to your Postiz queue, which publishes according to your Postiz schedule. You control the schedule within your Postiz dashboard.

Pro version adds options to post immediately, add to start/end of queue, or schedule for specific date/time.

= Will this work with the Gutenberg block editor? =

Yes. The plugin is fully compatible with Gutenberg (WordPress block editor), Classic Editor, and most page builders including Elementor, Divi, and Beaver Builder.

= Can I auto post WooCommerce products to social media? =

Yes. WooCommerce products are custom post types that can be shared to social media. 

Pro version includes special WooCommerce template tags to display product price, sale price, SKU, stock status, and other product data in social media messages.

= How do I include images in social media posts? =

Enable "Use Featured Image" in the plugin settings. The post's featured image will automatically attach to the social media update.

Pro version provides advanced image options:
* Multiple images per post
* Images from Media Gallery
* Images from post content
* Advanced Custom Fields images
* Custom image selection

= Does this work with scheduled WordPress posts? =

Yes. When a scheduled WordPress post publishes automatically, the plugin detects the publication and adds the content to your Postiz queue.

= Can I repost old content to social media? =

Pro version includes automatic evergreen content reposting. Configure how often to reshare old posts (days, weeks, or months), and the plugin automatically adds them back to your Postiz queue. Perfect for driving traffic to your best content.

= What happens if my Postiz queue is full? =

You'll see an error in the plugin log indicating the queue is full. Solutions:

1. Upgrade your Postiz plan for larger queue capacity
2. Manually publish or remove queued items from Postiz
3. Adjust how frequently content is added to Postiz

= Can I test without actually posting to social media? =

Yes. Enable "Test Mode" in General Settings. This logs what would be sent to Postiz without actually creating posts. Perfect for testing your message templates and settings.

= How do I see what was posted to Postiz? =

The plugin includes a logging system. View logs by:

1. Go to Postiz Auto Poster > Logs in your admin menu
2. View logs for all posts or filter by specific posts
3. See what was sent, when, and any errors encountered

Enable logging in Postiz Auto Poster > Settings > Log Settings.

= Does this work with custom post types? =

Yes. The plugin supports all public custom post types including:
* WooCommerce Products
* Events (The Events Calendar, Event Manager, Modern Events Calendar with Pro)
* Portfolio items
* Testimonials
* Any custom post type registered by themes or plugins

= Can I exclude specific posts from auto posting? =

Pro version includes conditional publishing based on:
* Post author
* Categories and tags
* Custom field values
* Custom taxonomies

You can also override settings on individual posts to disable auto posting selectively.

= Will this slow down my website? =

No. The plugin uses WordPress's standard HTTP API for communication with Postiz. For high-traffic sites, Pro version includes WP-Cron support to queue posts in the background without impacting page load times.

= What if I need help setting up the plugin? =

Free version support is available through the [WordPress support forums](https://wordpress.org/support/plugin/postiz-auto-poster/).

== Screenshots ==

1. Settings Screen when Plugin is first installed.
2. Settings Screen when Postiz is authorized.
3. Settings Screen showing available options for Posts.
4. Post-level Logging.

== Changelog ==

= 1.0.0 =
* First release.

== Upgrade Notice ==

