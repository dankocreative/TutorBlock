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
		categories,
		coursesPerCategory,
		columns,
		columnsTablet,
		showCategoryTitle,
		showCategoryDescription,
		showCategoryCourseCount,
		showViewAllLink,
		viewAllText,
		showRating,
		showPrice,
		showEnrollButton,
		orderBy,
		order,
		primaryColor,
		cardBorderRadius,
		sectionSpacing,
	} = attributes;

	const blockProps = useBlockProps( {
		className: 'tutorblock-cat-grid-wrapper',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Category Settings', 'tutorblock' ) }
					initialOpen={ true }
				>
					<TextControl
						label={ __( 'Categories (slugs)', 'tutorblock' ) }
						help={ __(
							'Enter comma-separated category slugs (e.g. programming,design,business). Leave blank to show all categories.',
							'tutorblock'
						) }
						value={ categories }
						onChange={ ( val ) =>
							setAttributes( { categories: val } )
						}
					/>
					<RangeControl
						label={ __( 'Courses Per Category', 'tutorblock' ) }
						value={ coursesPerCategory }
						onChange={ ( val ) =>
							setAttributes( { coursesPerCategory: val } )
						}
						min={ 1 }
						max={ 12 }
					/>
					<SelectControl
						label={ __( 'Order By', 'tutorblock' ) }
						value={ orderBy }
						options={ [
							{
								label: __( 'Date', 'tutorblock' ),
								value: 'date',
							},
							{
								label: __( 'Title', 'tutorblock' ),
								value: 'title',
							},
							{
								label: __( 'Popularity', 'tutorblock' ),
								value: 'popularity',
							},
							{
								label: __( 'Rating', 'tutorblock' ),
								value: 'rating',
							},
						] }
						onChange={ ( val ) =>
							setAttributes( { orderBy: val } )
						}
					/>
					<SelectControl
						label={ __( 'Order', 'tutorblock' ) }
						value={ order }
						options={ [
							{
								label: __( 'Descending', 'tutorblock' ),
								value: 'DESC',
							},
							{
								label: __( 'Ascending', 'tutorblock' ),
								value: 'ASC',
							},
						] }
						onChange={ ( val ) => setAttributes( { order: val } ) }
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Layout', 'tutorblock' ) }
					initialOpen={ false }
				>
					<RangeControl
						label={ __( 'Columns (Desktop)', 'tutorblock' ) }
						value={ columns }
						onChange={ ( val ) =>
							setAttributes( { columns: val } )
						}
						min={ 1 }
						max={ 6 }
					/>
					<RangeControl
						label={ __( 'Columns (Tablet)', 'tutorblock' ) }
						value={ columnsTablet }
						onChange={ ( val ) =>
							setAttributes( { columnsTablet: val } )
						}
						min={ 1 }
						max={ 4 }
					/>
					<RangeControl
						label={ __( 'Section Spacing (px)', 'tutorblock' ) }
						value={ sectionSpacing }
						onChange={ ( val ) =>
							setAttributes( { sectionSpacing: val } )
						}
						min={ 16 }
						max={ 120 }
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Section Header', 'tutorblock' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __( 'Show Category Title', 'tutorblock' ) }
						checked={ showCategoryTitle }
						onChange={ ( val ) =>
							setAttributes( { showCategoryTitle: val } )
						}
					/>
					<ToggleControl
						label={ __(
							'Show Category Description',
							'tutorblock'
						) }
						checked={ showCategoryDescription }
						onChange={ ( val ) =>
							setAttributes( { showCategoryDescription: val } )
						}
					/>
					<ToggleControl
						label={ __( 'Show Course Count', 'tutorblock' ) }
						checked={ showCategoryCourseCount }
						onChange={ ( val ) =>
							setAttributes( { showCategoryCourseCount: val } )
						}
					/>
					<ToggleControl
						label={ __( 'Show "View All" Link', 'tutorblock' ) }
						checked={ showViewAllLink }
						onChange={ ( val ) =>
							setAttributes( { showViewAllLink: val } )
						}
					/>
					{ showViewAllLink && (
						<TextControl
							label={ __( '"View All" Link Text', 'tutorblock' ) }
							value={ viewAllText }
							onChange={ ( val ) =>
								setAttributes( { viewAllText: val } )
							}
						/>
					) }
				</PanelBody>

				<PanelBody
					title={ __( 'Card Options', 'tutorblock' ) }
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
						🗂{ ' ' }
						{ __(
							'TutorBlock: Category Course Grid',
							'tutorblock'
						) }
					</span>
				</div>
				<ServerSideRender
					block="tutorblock/category-course-grid"
					attributes={ attributes }
					httpMethod="POST"
				/>
			</div>
		</>
	);
}
