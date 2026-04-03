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
		columns,
		columnsTablet,
		category,
		orderBy,
		order,
		showFilterBar,
		showPagination,
		showRating,
		showPrice,
		showInstructor,
		showEnrollButton,
		showMeta,
		primaryColor,
		cardBorderRadius,
		layout,
	} = attributes;

	const blockProps = useBlockProps( {
		className: 'tutorblock-grid-wrapper',
	} );

	return (
		<>
			<InspectorControls>
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
						max={ 48 }
					/>
					<TextControl
						label={ __( 'Filter by Category Slug', 'tutorblock' ) }
						help={ __(
							'Leave blank to show all. Use comma-separated slugs for multiple.',
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
							{
								label: __( 'Random', 'tutorblock' ),
								value: 'rand',
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
					<SelectControl
						label={ __( 'Card Style', 'tutorblock' ) }
						value={ layout }
						options={ [
							{
								label: __( 'Card (default)', 'tutorblock' ),
								value: 'card',
							},
							{
								label: __( 'List / Horizontal', 'tutorblock' ),
								value: 'list',
							},
							{
								label: __( 'Minimal', 'tutorblock' ),
								value: 'minimal',
							},
						] }
						onChange={ ( val ) => setAttributes( { layout: val } ) }
					/>
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
				</PanelBody>

				<PanelBody
					title={ __( 'Display Options', 'tutorblock' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __( 'Show Filter Bar', 'tutorblock' ) }
						help={ __(
							'Adds a category filter bar above the grid.',
							'tutorblock'
						) }
						checked={ showFilterBar }
						onChange={ ( val ) =>
							setAttributes( { showFilterBar: val } )
						}
					/>
					<ToggleControl
						label={ __( 'Show Pagination', 'tutorblock' ) }
						checked={ showPagination }
						onChange={ ( val ) =>
							setAttributes( { showPagination: val } )
						}
					/>
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
						label={ __(
							'Show Course Meta (lessons, duration)',
							'tutorblock'
						) }
						checked={ showMeta }
						onChange={ ( val ) =>
							setAttributes( { showMeta: val } )
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
						⊞ { __( 'TutorBlock: Course Grid', 'tutorblock' ) }
					</span>
				</div>
				<ServerSideRender
					block="tutorblock/course-grid"
					attributes={ attributes }
					httpMethod="POST"
				/>
			</div>
		</>
	);
}
