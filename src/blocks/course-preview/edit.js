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
		courseId,
		layout,
		showDescription,
		showInstructor,
		showStats,
		showRating,
		showRequirements,
		showCurriculum,
		showEnrollButton,
		enrollButtonText,
		primaryColor,
		accentColor,
		imagePosition,
	} = attributes;

	const blockProps = useBlockProps( {
		className: 'tutorblock-preview-wrapper',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Course Selection', 'tutorblock' ) }
					initialOpen={ true }
				>
					<TextControl
						label={ __( 'Course ID', 'tutorblock' ) }
						help={ __(
							'Enter the Post ID of the TutorLMS course to display. Find it in the course list URL.',
							'tutorblock'
						) }
						value={ courseId }
						onChange={ ( val ) =>
							setAttributes( {
								courseId: parseInt( val, 10 ) || 0,
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
						label={ __( 'Layout Style', 'tutorblock' ) }
						value={ layout }
						options={ [
							{
								label: __(
									'Horizontal (side-by-side)',
									'tutorblock'
								),
								value: 'horizontal',
							},
							{
								label: __( 'Vertical (stacked)', 'tutorblock' ),
								value: 'vertical',
							},
							{
								label: __(
									'Hero (full-width banner)',
									'tutorblock'
								),
								value: 'hero',
							},
							{
								label: __(
									'Cinematic (image + button overlay)',
									'tutorblock'
								),
								value: 'cinematic',
							},
						] }
						onChange={ ( val ) => setAttributes( { layout: val } ) }
					/>
					{ layout === 'horizontal' && (
						<SelectControl
							label={ __( 'Image Position', 'tutorblock' ) }
							value={ imagePosition }
							options={ [
								{
									label: __( 'Left', 'tutorblock' ),
									value: 'left',
								},
								{
									label: __( 'Right', 'tutorblock' ),
									value: 'right',
								},
							] }
							onChange={ ( val ) =>
								setAttributes( { imagePosition: val } )
							}
						/>
					) }
				</PanelBody>

				<PanelBody
					title={ __( 'Display Options', 'tutorblock' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __( 'Show Description', 'tutorblock' ) }
						checked={ showDescription }
						onChange={ ( val ) =>
							setAttributes( { showDescription: val } )
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
						label={ __( 'Show Course Stats', 'tutorblock' ) }
						help={ __(
							'Students enrolled, lessons, duration, level.',
							'tutorblock'
						) }
						checked={ showStats }
						onChange={ ( val ) =>
							setAttributes( { showStats: val } )
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
						label={ __( 'Show Requirements', 'tutorblock' ) }
						checked={ showRequirements }
						onChange={ ( val ) =>
							setAttributes( { showRequirements: val } )
						}
					/>
					<ToggleControl
						label={ __( 'Show Curriculum Preview', 'tutorblock' ) }
						checked={ showCurriculum }
						onChange={ ( val ) =>
							setAttributes( { showCurriculum: val } )
						}
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Enroll Button', 'tutorblock' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __( 'Show Enroll Button', 'tutorblock' ) }
						checked={ showEnrollButton }
						onChange={ ( val ) =>
							setAttributes( { showEnrollButton: val } )
						}
					/>
					{ showEnrollButton && (
						<TextControl
							label={ __( 'Button Text', 'tutorblock' ) }
							value={ enrollButtonText }
							onChange={ ( val ) =>
								setAttributes( { enrollButtonText: val } )
							}
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
						{ __( 'Accent Color (stars, badges)', 'tutorblock' ) }
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
						📋 { __( 'TutorBlock: Course Preview', 'tutorblock' ) }
					</span>
					{ ! courseId && (
						<p className="tutorblock-editor-notice">
							{ __(
								'⚠️ Set a Course ID in the block settings to preview a course.',
								'tutorblock'
							) }
						</p>
					) }
				</div>
				{ courseId > 0 && (
					<ServerSideRender
						block="tutorblock/course-preview"
						attributes={ attributes }
						httpMethod="POST"
					/>
				) }
			</div>
		</>
	);
}
