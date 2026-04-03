import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	ToggleControl,
	SelectControl,
	ColorPicker,
	TextControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit( { attributes, setAttributes } ) {
	const {
		numberOfCourses,
		category,
		orderBy,
		order,
		slidesToShow,
		slidesToShowTablet,
		slidesToShowMobile,
		autoplay,
		autoplaySpeed,
		showArrows,
		showDots,
		showRating,
		showPrice,
		showInstructor,
		showEnrollButton,
		primaryColor,
		cardBorderRadius,
	} = attributes;

	const blockProps = useBlockProps( {
		className: 'tutorblock-carousel-wrapper',
	} );

	const orderByOptions = [
		{ label: __( 'Date', 'tutorblock' ), value: 'date' },
		{ label: __( 'Title', 'tutorblock' ), value: 'title' },
		{ label: __( 'Popularity', 'tutorblock' ), value: 'popularity' },
		{ label: __( 'Rating', 'tutorblock' ), value: 'rating' },
		{ label: __( 'Random', 'tutorblock' ), value: 'rand' },
		{ label: __( 'Menu Order', 'tutorblock' ), value: 'menu_order' },
	];

	const orderOptions = [
		{ label: __( 'Descending', 'tutorblock' ), value: 'DESC' },
		{ label: __( 'Ascending', 'tutorblock' ), value: 'ASC' },
	];

	return (
		<>
			<InspectorControls>
				{ /* Content Panel */ }
				<PanelBody
					title={ __( 'Content Settings', 'tutorblock' ) }
					initialOpen={ true }
				>
					<RangeControl
						label={ __( 'Number of Courses', 'tutorblock' ) }
						value={ numberOfCourses }
						onChange={ ( val ) =>
							setAttributes( { numberOfCourses: val } )
						}
						min={ 1 }
						max={ 24 }
					/>
					<TextControl
						label={ __( 'Filter by Category Slug', 'tutorblock' ) }
						help={ __(
							'Leave blank to show all categories.',
							'tutorblock'
						) }
						value={ category }
						onChange={ ( val ) =>
							setAttributes( { category: val } )
						}
					/>
					<SelectControl
						label={ __( 'Order By', 'tutorblock' ) }
						value={ orderBy }
						options={ orderByOptions }
						onChange={ ( val ) =>
							setAttributes( { orderBy: val } )
						}
					/>
					<SelectControl
						label={ __( 'Order', 'tutorblock' ) }
						value={ order }
						options={ orderOptions }
						onChange={ ( val ) => setAttributes( { order: val } ) }
					/>
				</PanelBody>

				{ /* Carousel Display Panel */ }
				<PanelBody
					title={ __( 'Carousel Settings', 'tutorblock' ) }
					initialOpen={ false }
				>
					<RangeControl
						label={ __( 'Slides to Show (Desktop)', 'tutorblock' ) }
						value={ slidesToShow }
						onChange={ ( val ) =>
							setAttributes( { slidesToShow: val } )
						}
						min={ 1 }
						max={ 6 }
					/>
					<RangeControl
						label={ __( 'Slides to Show (Tablet)', 'tutorblock' ) }
						value={ slidesToShowTablet }
						onChange={ ( val ) =>
							setAttributes( { slidesToShowTablet: val } )
						}
						min={ 1 }
						max={ 4 }
					/>
					<RangeControl
						label={ __( 'Slides to Show (Mobile)', 'tutorblock' ) }
						value={ slidesToShowMobile }
						onChange={ ( val ) =>
							setAttributes( { slidesToShowMobile: val } )
						}
						min={ 1 }
						max={ 2 }
					/>
					<ToggleControl
						label={ __( 'Autoplay', 'tutorblock' ) }
						checked={ autoplay }
						onChange={ ( val ) =>
							setAttributes( { autoplay: val } )
						}
					/>
					{ autoplay && (
						<RangeControl
							label={ __( 'Autoplay Speed (ms)', 'tutorblock' ) }
							value={ autoplaySpeed }
							onChange={ ( val ) =>
								setAttributes( { autoplaySpeed: val } )
							}
							min={ 1000 }
							max={ 8000 }
							step={ 500 }
						/>
					) }
					<ToggleControl
						label={ __( 'Show Navigation Arrows', 'tutorblock' ) }
						checked={ showArrows }
						onChange={ ( val ) =>
							setAttributes( { showArrows: val } )
						}
					/>
					<ToggleControl
						label={ __( 'Show Dot Indicators', 'tutorblock' ) }
						checked={ showDots }
						onChange={ ( val ) =>
							setAttributes( { showDots: val } )
						}
					/>
				</PanelBody>

				{ /* Card Display Panel */ }
				<PanelBody
					title={ __( 'Card Display', 'tutorblock' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __( 'Show Star Rating', 'tutorblock' ) }
						checked={ showRating }
						onChange={ ( val ) =>
							setAttributes( { showRating: val } )
						}
					/>
					<ToggleControl
						label={ __( 'Show Price', 'tutorblock' ) }
						checked={ showPrice }
						onChange={ ( val ) =>
							setAttributes( { showPrice: val } )
						}
					/>
					<ToggleControl
						label={ __( 'Show Instructor', 'tutorblock' ) }
						checked={ showInstructor }
						onChange={ ( val ) =>
							setAttributes( { showInstructor: val } )
						}
					/>
					<ToggleControl
						label={ __( 'Show Enroll Button', 'tutorblock' ) }
						checked={ showEnrollButton }
						onChange={ ( val ) =>
							setAttributes( { showEnrollButton: val } )
						}
					/>
					<RangeControl
						label={ __( 'Card Border Radius (px)', 'tutorblock' ) }
						value={ cardBorderRadius }
						onChange={ ( val ) =>
							setAttributes( { cardBorderRadius: val } )
						}
						min={ 0 }
						max={ 24 }
					/>
				</PanelBody>

				{ /* Style Panel */ }
				<PanelBody
					title={ __( 'Styling', 'tutorblock' ) }
					initialOpen={ false }
				>
					<p className="components-base-control__label">
						{ __( 'Primary Color', 'tutorblock' ) }
					</p>
					<ColorPicker
						color={ primaryColor }
						onChange={ ( val ) =>
							setAttributes( { primaryColor: val } )
						}
						enableAlpha={ false }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="tutorblock-editor-label">
					<span className="tutorblock-editor-badge">
						🎡 { __( 'TutorBlock: Course Carousel', 'tutorblock' ) }
					</span>
				</div>
				<ServerSideRender
					block="tutorblock/course-carousel"
					attributes={ attributes }
					httpMethod="POST"
				/>
			</div>
		</>
	);
}
