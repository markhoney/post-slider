/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, useInnerBlocksProps, InspectorControls, BlockContextProvider } from '@wordpress/block-editor';

import { PanelBody, RangeControl, ToggleControl, SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as blockEditorStore } from '@wordpress/block-editor';
import { store as coreStore } from '@wordpress/core-data';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit(props) {
	const { attributes, setAttributes, clientId, context } = props;
	const {
		slidesPerView,
		spaceBetween,
		autoplay,
		autoplayDelay,
		loop,
		navigation,
		pagination,
		paginationType,
		speed,
		effect,
		centeredSlides,
		enableBreakpoints,
		customParams,
	} = attributes;

	const { query } = context;

	// Get posts from the query
	const { posts } = useSelect(
		(select) => {
			const { getEntityRecords } = select(coreStore);
			const { getBlocks } = select(blockEditorStore);
			
			const queryArgs = {per_page: query?.perPage || 3, _embed: 'wp:featuredmedia'};
			if (query?.author) queryArgs.author = query.author;
			if (query?.search) queryArgs.search = query.search;
			if (query?.taxQuery) Object.keys(query.taxQuery).forEach((taxonomy) => queryArgs[taxonomy] = query.taxQuery[taxonomy]);
			if (query?.order) queryArgs.order = query.order;
			if (query?.orderBy) queryArgs.orderby = query.orderBy;
			return {
				posts: getEntityRecords('postType', query?.postType || 'post', queryArgs),
				blocks: getBlocks(clientId),
			};
		},
		[query, clientId]
	);

	const blockProps = useBlockProps({
		className: 'query-loop-slider-editor'
	});

	const innerBlocksProps = useInnerBlocksProps({}, {
		allowedBlocks: undefined, // Allow all blocks like Post Template does
		renderAppender: undefined, // Use default appender
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Slider Settings', 'query-loop-slider')} initialOpen={true}>
					<RangeControl
						label={__('Slides Per View', 'query-loop-slider')}
						value={slidesPerView}
						onChange={(value) => setAttributes({ slidesPerView: value })}
						min={1}
						max={10}
						help={__('Number of slides visible at the same time', 'query-loop-slider')}
					/>
					<RangeControl
						label={__('Space Between (px)', 'query-loop-slider')}
						value={spaceBetween}
						onChange={(value) => setAttributes({ spaceBetween: value })}
						min={0}
						max={100}
						help={__('Distance between slides in pixels', 'query-loop-slider')}
					/>
					<RangeControl
						label={__('Speed (ms)', 'query-loop-slider')}
						value={speed}
						onChange={(value) => setAttributes({ speed: value })}
						min={100}
						max={2000}
						step={100}
						help={__('Duration of transition animation', 'query-loop-slider')}
					/>
					<ToggleControl
						label={__('Centered Slides', 'query-loop-slider')}
						checked={centeredSlides}
						onChange={(value) => setAttributes({ centeredSlides: value })}
						help={__('Center the active slide', 'query-loop-slider')}
					/>
				</PanelBody>

				<PanelBody title={__('Autoplay', 'query-loop-slider')} initialOpen={false}>
					<ToggleControl
						label={__('Enable Autoplay', 'query-loop-slider')}
						checked={autoplay}
						onChange={(value) => setAttributes({ autoplay: value })}
					/>
					{autoplay && (
						<RangeControl
							label={__('Autoplay Delay (ms)', 'query-loop-slider')}
							value={autoplayDelay}
							onChange={(value) => setAttributes({ autoplayDelay: value })}
							min={1000}
							max={10000}
							step={500}
							help={__('Delay between transitions', 'query-loop-slider')}
						/>
					)}
				</PanelBody>

				<PanelBody title={__('Navigation', 'query-loop-slider')} initialOpen={false}>
					<ToggleControl
						label={__('Show Navigation Arrows', 'query-loop-slider')}
						checked={navigation}
						onChange={(value) => setAttributes({ navigation: value })}
					/>
					<ToggleControl
						label={__('Show Pagination', 'query-loop-slider')}
						checked={pagination}
						onChange={(value) => setAttributes({ pagination: value })}
					/>
					{pagination && (
						<SelectControl
							label={__('Pagination Type', 'query-loop-slider')}
							value={paginationType}
							options={[
								{ label: __('Bullets', 'query-loop-slider'), value: 'bullets' },
								{ label: __('Fraction', 'query-loop-slider'), value: 'fraction' },
								{ label: __('Progress Bar', 'query-loop-slider'), value: 'progressbar' },
							]}
							onChange={(value) => setAttributes({ paginationType: value })}
						/>
					)}
					<ToggleControl
						label={__('Loop', 'query-loop-slider')}
						checked={loop}
						onChange={(value) => setAttributes({ loop: value })}
						help={__('Enable continuous loop mode', 'query-loop-slider')}
					/>
				</PanelBody>

				<PanelBody title={__('Effects', 'query-loop-slider')} initialOpen={false}>
					<SelectControl
						label={__('Transition Effect', 'query-loop-slider')}
						value={effect}
						options={[
							{ label: __('Slide', 'query-loop-slider'), value: 'slide' },
							{ label: __('Fade', 'query-loop-slider'), value: 'fade' },
							{ label: __('Cube', 'query-loop-slider'), value: 'cube' },
							{ label: __('Coverflow', 'query-loop-slider'), value: 'coverflow' },
							{ label: __('Flip', 'query-loop-slider'), value: 'flip' },
						]}
						onChange={(value) => setAttributes({ effect: value })}
					/>
				</PanelBody>

				<PanelBody title={__('Custom Swiper Parameters', 'query-loop-slider')} initialOpen={false}>
					<textarea
						style={{ width: '100%', minHeight: '80px', fontFamily: 'monospace' }}
						value={customParams}
						onChange={e => setAttributes({ customParams: e.target.value })}
						placeholder={__('Enter custom Swiper parameters as JSON', 'query-loop-slider')}
					/>
				<p style={{ fontSize: '12px', color: '#757575', marginTop: '4px' }}>
					{__('These parameters will be merged with the options set above. Invalid JSON will be ignored.', 'query-loop-slider')}
					{' '}
					<a href="https://swiperjs.com/swiper-api" target="_blank" rel="noopener noreferrer">
						{__('View Swiper API documentation', 'query-loop-slider')}
					</a>
				</p>
			</PanelBody>				<PanelBody title={__('Responsive Breakpoints', 'query-loop-slider')} initialOpen={false}>
					<ToggleControl
						label={__('Enable Custom Breakpoints', 'query-loop-slider')}
						checked={enableBreakpoints}
						onChange={(value) => setAttributes({ enableBreakpoints: value })}
						help={__('Override slides per view at different screen sizes', 'query-loop-slider')}
					/>
					<p style={{ fontSize: '12px', color: '#757575', marginTop: '8px' }}>
						{__('When enabled, the slider will show:', 'query-loop-slider')}
						<br />
						{__('• 1 slide on mobile (< 640px)', 'query-loop-slider')}
						<br />
						{__('• 2 slides on tablet (640-1023px)', 'query-loop-slider')}
						<br />
						{__('• 3 slides on desktop (≥ 1024px)', 'query-loop-slider')}
					</p>
				</PanelBody>
			</InspectorControls>

			<div {...blockProps}>
				{posts && posts.length > 0 ? (
					<BlockContextProvider value={{ postId: posts[0].id, postType: query?.postType || posts[0]?.type || 'post' }}>
						<div {...innerBlocksProps} />
					</BlockContextProvider>
				) : (
					<div {...innerBlocksProps} />
				)}
			</div>
		</>
	);
}
