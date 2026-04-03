import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	SelectControl,
	ColorPicker,
	TextControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit( { attributes, setAttributes } ) {
	const {
		showTotalCourses,
		showTotalStudents,
		showTotalInstructors,
		showTotalReviews,
		showTotalCategories,
		labelCourses,
		labelStudents,
		labelInstructors,
		labelReviews,
		labelCategories,
		layout,
		style,
		primaryColor,
		secondaryColor,
		animated,
		showIcons,
		iconStyle,
	} = attributes;

	const blockProps = useBlockProps( {
		className: 'tutorblock-stats-wrapper',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Stats to Display', 'tutorblock' ) }
					initialOpen={ true }
				>
					<ToggleControl
						label={ __( 'Total Courses', 'tutorblock' ) }
						checked={ showTotalCourses }
						onChange={ ( val ) =>
							setAttributes( { showTotalCourses: val } )
						}
					/>
					{ showTotalCourses && (
						<TextControl
							label={ __( 'Courses Label', 'tutorblock' ) }
							value={ labelCourses }
							onChange={ ( val ) =>
								setAttributes( { labelCourses: val } )
							}
						/>
					) }
					<ToggleControl
						label={ __( 'Total Students', 'tutorblock' ) }
						checked={ showTotalStudents }
						onChange={ ( val ) =>
							setAttributes( { showTotalStudents: val } )
						}
					/>
					{ showTotalStudents && (
						<TextControl
							label={ __( 'Students Label', 'tutorblock' ) }
							value={ labelStudents }
							onChange={ ( val ) =>
								setAttributes( { labelStudents: val } )
							}
						/>
					) }
					<ToggleControl
						label={ __( 'Total Instructors', 'tutorblock' ) }
						checked={ showTotalInstructors }
						onChange={ ( val ) =>
							setAttributes( { showTotalInstructors: val } )
						}
					/>
					{ showTotalInstructors && (
						<TextControl
							label={ __( 'Instructors Label', 'tutorblock' ) }
							value={ labelInstructors }
							onChange={ ( val ) =>
								setAttributes( { labelInstructors: val } )
							}
						/>
					) }
					<ToggleControl
						label={ __( 'Total Reviews', 'tutorblock' ) }
						checked={ showTotalReviews }
						onChange={ ( val ) =>
							setAttributes( { showTotalReviews: val } )
						}
					/>
					{ showTotalReviews && (
						<TextControl
							label={ __( 'Reviews Label', 'tutorblock' ) }
							value={ labelReviews }
							onChange={ ( val ) =>
								setAttributes( { labelReviews: val } )
							}
						/>
					) }
					<ToggleControl
						label={ __( 'Total Categories', 'tutorblock' ) }
						checked={ showTotalCategories }
						onChange={ ( val ) =>
							setAttributes( { showTotalCategories: val } )
						}
					/>
					{ showTotalCategories && (
						<TextControl
							label={ __( 'Categories Label', 'tutorblock' ) }
							value={ labelCategories }
							onChange={ ( val ) =>
								setAttributes( { labelCategories: val } )
							}
						/>
					) }
				</PanelBody>

				<PanelBody
					title={ __( 'Layout & Style', 'tutorblock' ) }
					initialOpen={ false }
				>
					<SelectControl
						label={ __( 'Layout', 'tutorblock' ) }
						value={ layout }
						options={ [
							{
								label: __(
									'Horizontal (inline)',
									'tutorblock'
								),
								value: 'horizontal',
							},
							{
								label: __( 'Grid 2×2', 'tutorblock' ),
								value: 'grid',
							},
							{
								label: __( 'Vertical Stack', 'tutorblock' ),
								value: 'vertical',
							},
						] }
						onChange={ ( val ) => setAttributes( { layout: val } ) }
					/>
					<SelectControl
						label={ __( 'Visual Style', 'tutorblock' ) }
						value={ style }
						options={ [
							{
								label: __( 'Default (outlined)', 'tutorblock' ),
								value: 'default',
							},
							{
								label: __( 'Filled Cards', 'tutorblock' ),
								value: 'filled',
							},
							{
								label: __( 'Gradient', 'tutorblock' ),
								value: 'gradient',
							},
							{
								label: __(
									'Minimal / Borderless',
									'tutorblock'
								),
								value: 'minimal',
							},
						] }
						onChange={ ( val ) => setAttributes( { style: val } ) }
					/>
					<ToggleControl
						label={ __( 'Animated Number Counter', 'tutorblock' ) }
						checked={ animated }
						onChange={ ( val ) =>
							setAttributes( { animated: val } )
						}
					/>
					<ToggleControl
						label={ __( 'Show Icons', 'tutorblock' ) }
						checked={ showIcons }
						onChange={ ( val ) =>
							setAttributes( { showIcons: val } )
						}
					/>
					{ showIcons && (
						<SelectControl
							label={ __( 'Icon Style', 'tutorblock' ) }
							value={ iconStyle }
							options={ [
								{
									label: __( 'Emoji', 'tutorblock' ),
									value: 'emoji',
								},
								{
									label: __( 'SVG', 'tutorblock' ),
									value: 'svg',
								},
							] }
							onChange={ ( val ) =>
								setAttributes( { iconStyle: val } )
							}
						/>
					) }
				</PanelBody>

				<PanelBody
					title={ __( 'Colors', 'tutorblock' ) }
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
					<p
						className="components-base-control__label"
						style={ { marginTop: '1rem' } }
					>
						{ __( 'Secondary / Gradient End Color', 'tutorblock' ) }
					</p>
					<ColorPicker
						color={ secondaryColor }
						onChange={ ( val ) =>
							setAttributes( { secondaryColor: val } )
						}
						enableAlpha={ false }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="tutorblock-editor-label">
					<span className="tutorblock-editor-badge">
						📊{ ' ' }
						{ __(
							'TutorBlock: Course Platform Stats',
							'tutorblock'
						) }
					</span>
				</div>
				<ServerSideRender
					block="tutorblock/course-stats"
					attributes={ attributes }
					httpMethod="POST"
				/>
			</div>
		</>
	);
}
