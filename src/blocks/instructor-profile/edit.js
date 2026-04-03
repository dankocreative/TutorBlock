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
		instructorId,
		layout,
		showBio,
		showStats,
		showSocialLinks,
		showCourses,
		coursesToShow,
		showRatings,
		primaryColor,
		accentColor,
	} = attributes;

	const blockProps = useBlockProps( {
		className: 'tutorblock-instructor-wrapper',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Instructor Selection', 'tutorblock' ) }
					initialOpen={ true }
				>
					<TextControl
						label={ __( 'Instructor User ID', 'tutorblock' ) }
						help={ __(
							'Enter the WordPress user ID of the TutorLMS instructor.',
							'tutorblock'
						) }
						value={ instructorId }
						onChange={ ( val ) =>
							setAttributes( {
								instructorId: parseInt( val, 10 ) || 0,
							} )
						}
						min={ 0 }
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Layout', 'tutorblock' ) }
					initialOpen={ false }
				>
					<SelectControl
						label={ __( 'Profile Layout', 'tutorblock' ) }
						value={ layout }
						options={ [
							{
								label: __( 'Card', 'tutorblock' ),
								value: 'card',
							},
							{
								label: __( 'Horizontal', 'tutorblock' ),
								value: 'horizontal',
							},
							{
								label: __( 'Minimal', 'tutorblock' ),
								value: 'minimal',
							},
						] }
						onChange={ ( val ) => setAttributes( { layout: val } ) }
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Display Options', 'tutorblock' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __( 'Show Bio / About', 'tutorblock' ) }
						checked={ showBio }
						onChange={ ( val ) =>
							setAttributes( { showBio: val } )
						}
					/>
					<ToggleControl
						label={ __(
							'Show Statistics (students, courses, reviews)',
							'tutorblock'
						) }
						checked={ showStats }
						onChange={ ( val ) =>
							setAttributes( { showStats: val } )
						}
					/>
					<ToggleControl
						label={ __( 'Show Social Links', 'tutorblock' ) }
						checked={ showSocialLinks }
						onChange={ ( val ) =>
							setAttributes( { showSocialLinks: val } )
						}
					/>
					<ToggleControl
						label={ __( 'Show Overall Rating', 'tutorblock' ) }
						checked={ showRatings }
						onChange={ ( val ) =>
							setAttributes( { showRatings: val } )
						}
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Courses', 'tutorblock' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __( 'Show Instructor Courses', 'tutorblock' ) }
						checked={ showCourses }
						onChange={ ( val ) =>
							setAttributes( { showCourses: val } )
						}
					/>
					{ showCourses && (
						<RangeControl
							label={ __(
								'Number of Courses to Show',
								'tutorblock'
							) }
							value={ coursesToShow }
							onChange={ ( val ) =>
								setAttributes( { coursesToShow: val } )
							}
							min={ 1 }
							max={ 12 }
						/>
					) }
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
					<p
						className="components-base-control__label"
						style={ { marginTop: '1rem' } }
					>
						{ __( 'Accent Color', 'tutorblock' ) }
					</p>
					<ColorPicker
						color={ accentColor }
						onChange={ ( val ) =>
							setAttributes( { accentColor: val } )
						}
						enableAlpha={ false }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="tutorblock-editor-label">
					<span className="tutorblock-editor-badge">
						👤{ ' ' }
						{ __( 'TutorBlock: Instructor Profile', 'tutorblock' ) }
					</span>
					{ ! instructorId && (
						<p className="tutorblock-editor-notice">
							{ __(
								'⚠️ Set an Instructor User ID in the block settings.',
								'tutorblock'
							) }
						</p>
					) }
				</div>
				{ instructorId > 0 && (
					<ServerSideRender
						block="tutorblock/instructor-profile"
						attributes={ attributes }
						httpMethod="POST"
					/>
				) }
			</div>
		</>
	);
}
