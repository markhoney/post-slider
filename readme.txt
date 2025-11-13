=== Post Slider ===
Contributors:      Mark Honeychurch
Tags:              block, query, loop, slider, carousel, flip, swiper
Requires at least: 6.7
Tested up to:      6.8.3
Requires PHP:      7.4
Stable tag:        0.1.0
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

A WordPress block that provides similar functionality to the Post Template, but displays posts in a slider/carousel format.

== Description ==

Post Slider is a block for modern WordPress that lets you display the results of a Query Loop as a responsive, touch-friendly slider powered by [Swiper](https://swiperjs.com/).

The block is designed to be placed inside a `Query Loop` and will render the inner blocks you define (for each post) as individual slides on the front end. Assets are only loaded on pages where the block appears.

=== Highlights ===

* Works inside the `Query Loop` block (inherits the query).
* Server-side rendering that respects your inner blocks as the slide template.
* Uses Swiper for smooth, accessible, mobile-friendly sliders.
* Loads Swiper assets only when the block is present on the page.
* Configurable options: slides per view, spacing, autoplay, navigation, pagination, speed, effects, centered slides, breakpoints, and more.
* Supports core block styling options (spacing, color, typography, dimensions, border, shadows, etc.).

=== Requirements ===

* WordPress 6.7 or newer
* PHP 7.4 or newer

== Installation ==

1. Upload the plugin to `/wp-content/plugins/post-slider` or install it via the Plugins screen.
2. Activate the plugin via the “Plugins” screen.

=== From source (developers) ===

If you’re working from source:

```
npm install
npm run build
```

This compiles the block to `build/` and copies the required Swiper assets. Use `npm start` for a watcher during development, or `npm run plugin-zip` to produce a distributable ZIP.

== Usage ==

1. Add a `Query Loop` block to your page or template.
2. Inside the Query Loop, insert the “Post Slider” block.
3. Build the slide template by adding inner blocks (e.g., Post Featured Image, Post Title, Post Excerpt, Buttons, etc.).
4. Configure slider settings in the block sidebar.

=== Settings (key options) ===

* Slides per view: Number of slides visible at once (default: 1).
* Space between: Gap between slides in pixels (default: 0).
* Autoplay: Enable/disable autoplay; set delay in ms (default: 3000).
* Loop: Repeat slides continuously (default: true).
* Navigation: Show previous/next arrows (default: true).
* Pagination: Show bullets/fraction/progressbar (default: bullets).
* Speed: Transition speed in ms (default: 300).
* Effect: `slide`, `fade`, `cube`, `coverflow`, or `flip`.
* Centered slides: Center the active slide (default: false).
* Breakpoints: Toggle “Enable Breakpoints” to apply sensible defaults (640/768/1024). You can also fully customize via Custom Params.
* Custom params (JSON): Advanced Swiper configuration merged into the generated config. Example:

```
{
	"grabCursor": true,
	"breakpoints": {
		"640": { "slidesPerView": 1 },
		"768": { "slidesPerView": 2 },
		"1024": { "slidesPerView": 4 }
	}
}
```

Note: Invalid JSON is ignored. When using certain effects (e.g., `fade`, `cube`, `flip`), slides per view is forced to 1 by Swiper.

== Frequently Asked Questions ==

= Why are no posts showing? =

The block must be placed inside a `Query Loop` so it can inherit a query and iterate posts. Ensure your Query Loop is returning posts for the current context.

= Does it load Swiper on every page? =

No. Swiper CSS/JS is registered globally but only enqueued on pages where the `Post Slider` block is present.

= Can I style slides with theme tools? =

Yes. The block supports core styling controls (spacing, color, typography, dimensions, border, shadows). Use inner blocks to control the slide markup and style.

= How do I customize breakpoints further? =

Either enable the built-in “Enable Breakpoints” option for sensible defaults, or set a custom `breakpoints` object via “Custom params” JSON.

== Screenshots ==

1. Example of a three-column post slider with navigation and pagination.
2. Editing the slide template using core post blocks inside the Query Loop.

== Changelog ==

= 0.1.0 =
* Initial release.

== Credits ==

Powered by [Swiper](https://swiperjs.com/).
