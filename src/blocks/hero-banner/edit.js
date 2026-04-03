import { __ } from '@wordpress/i18n';
import {
	InspectorControls,
	useBlockProps,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody,
	Button,
	RangeControl,
	ToggleControl,
	SelectControl,
	ColorPicker,
	TextControl,
	TextareaControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit( { attributes, setAttributes } ) {
	const {
		backgroundImageUrl,
		backgroundImageId,
		videoUrl,
		overlayColor,
		overlayOpacity,
		headline,
		subheadline,
		buttonText,
		buttonUrl,
		buttonColor,
		buttonTextColor,
		showSecondaryButton,
		secondaryButtonText,
		secondaryButtonUrl,
		textAlign,
		textColor,
		minHeight,
		contentWidth,
		courseId,
		showCourseStats,
		tagline,
		gradientDirection,
	} = attributes;

	const blockProps = useBlockProps( {
		className: 'tutorblock-hero-wrapper',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Background', 'tutorblock' ) }
					initialOpen={ true }
				>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) =>
								setAttributes( {
									backgroundImageUrl: media.url,
									backgroundImageId: media.id,
								} )
							}
							allowedTypes={ [ 'image' ] }
							value={ backgroundImageId }
							render={ ( { open } ) => (
								<div>
									{ backgroundImageUrl && (
										<img
											src={ backgroundImageUrl }
											alt=""
											style={ {
												width: '100%',
												marginBottom: '8px',
												borderRadius: '4px',
											} }
										/>
									) }
									<Button
										onClick={ open }
										variant="secondary"
										isSmall
									>
										{ backgroundImageUrl
											? __( 'Change Image', 'tutorblock' )
											: __(
													'Select Background Image',
													'tutorblock'
											  ) }
									</Button>
									{ backgroundImageUrl && (
										<Button
											onClick={ () =>
												setAttributes( {
													backgroundImageUrl: '',
													backgroundImageId: 0,
												} )
											}
											variant="link"
											isDestructive
											isSmall
											style={ { marginLeft: '8px' } }
										>
											{ __( 'Remove', 'tutorblock' ) }
										</Button>
									) }
								</div>
							) }
						/>
					</MediaUploadCheck>

					<TextControl
						label={ __( 'Background Video URL', 'tutorblock' ) }
						help={ __(
							'YouTube, Vimeo, or direct video URL. Plays muted & looped behind the content.',
							'tutorblock'
						) }
						value={ videoUrl }
						onChange={ ( val ) =>
							setAttributes( { videoUrl: val } )
						}
					/>

					<SelectControl
						label={ __( 'Overlay Gradient', 'tutorblock' ) }
						value={ gradientDirection }
						options={ [
							{
								label: __( 'Center (vignette)', 'tutorblock' ),
								value: 'center',
							},
							{
								label: __( 'Bottom to top', 'tutorblock' ),
								value: 'bottom',
							},
							{
								label: __( 'Left to right', 'tutorblock' ),
								value: 'left',
							},
							{
								label: __( 'Solid', 'tutorblock' ),
								value: 'solid',
							},
						] }
						onChange={ ( val ) =>
							setAttributes( { gradientDirection: val } )
						}
					/>

					<p
						style={ {
							margin: '0 0 4px',
							fontSize: '11px',
							fontWeight: 600,
							textTransform: 'uppercase',
						} }
					>
						{ __( 'Overlay Color', 'tutorblock' ) }
					</p>
					<ColorPicker
						color={ overlayColor }
						onChange={ ( val ) =>
							setAttributes( { overlayColor: val } )
						}
						enableAlpha={ false }
					/>

					<RangeControl
						label={ __( 'Overlay Opacity (%)', 'tutorblock' ) }
						value={ overlayOpacity }
						onChange={ ( val ) =>
							setAttributes( { overlayOpacity: val } )
						}
						min={ 0 }
						max={ 95 }
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Content', 'tutorblock' ) }
					initialOpen={ false }
				>
					<TextControl
						label={ __( 'Tagline (above headline)', 'tutorblock' ) }
						value={ tagline }
						onChange={ ( val ) =>
							setAttributes( { tagline: val } )
						}
					/>
					<TextControl
						label={ __( 'Headline', 'tutorblock' ) }
						value={ headline }
						onChange={ ( val ) =>
							setAttributes( { headline: val } )
						}
					/>
					<TextareaControl
						label={ __( 'Sub-headline', 'tutorblock' ) }
						value={ subheadline }
						onChange={ ( val ) =>
							setAttributes( { subheadline: val } )
						}
					/>
					<SelectControl
						label={ __( 'Text Alignment', 'tutorblock' ) }
						value={ textAlign }
						options={ [
							{
								label: __( 'Left', 'tutorblock' ),
								value: 'left',
							},
							{
								label: __( 'Center', 'tutorblock' ),
								value: 'center',
							},
							{
								label: __( 'Right', 'tutorblock' ),
								value: 'right',
							},
						] }
						onChange={ ( val ) =>
							setAttributes( { textAlign: val } )
						}
					/>
					<p
						style={ {
							margin: '0 0 4px',
							fontSize: '11px',
							fontWeight: 600,
							textTransform: 'uppercase',
						} }
					>
						{ __( 'Text Color', 'tutorblock' ) }
					</p>
					<ColorPicker
						color={ textColor }
						onChange={ ( val ) =>
							setAttributes( { textColor: val } )
						}
						enableAlpha={ false }
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Primary CTA Button', 'tutorblock' ) }
					initialOpen={ false }
				>
					<TextControl
						label={ __( 'Button Text', 'tutorblock' ) }
						value={ buttonText }
						onChange={ ( val ) =>
							setAttributes( { buttonText: val } )
						}
					/>
					<TextControl
						label={ __( 'Button URL', 'tutorblock' ) }
						value={ buttonUrl }
						onChange={ ( val ) =>
							setAttributes( { buttonUrl: val } )
						}
					/>
					<p
						style={ {
							margin: '0 0 4px',
							fontSize: '11px',
							fontWeight: 600,
							textTransform: 'uppercase',
						} }
					>
						{ __( 'Button Background', 'tutorblock' ) }
					</p>
					<ColorPicker
						color={ buttonColor }
						onChange={ ( val ) =>
							setAttributes( { buttonColor: val } )
						}
						enableAlpha={ false }
					/>
					<p
						style={ {
							margin: '0 0 4px',
							fontSize: '11px',
							fontWeight: 600,
							textTransform: 'uppercase',
						} }
					>
						{ __( 'Button Text Color', 'tutorblock' ) }
					</p>
					<ColorPicker
						color={ buttonTextColor }
						onChange={ ( val ) =>
							setAttributes( { buttonTextColor: val } )
						}
						enableAlpha={ false }
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Secondary Button', 'tutorblock' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __( 'Show Secondary Button', 'tutorblock' ) }
						checked={ showSecondaryButton }
						onChange={ ( val ) =>
							setAttributes( { showSecondaryButton: val } )
						}
					/>
					{ showSecondaryButton && (
						<>
							<TextControl
								label={ __(
									'Secondary Button Text',
									'tutorblock'
								) }
								value={ secondaryButtonText }
								onChange={ ( val ) =>
									setAttributes( {
										secondaryButtonText: val,
									} )
								}
							/>
							<TextControl
								label={ __(
									'Secondary Button URL',
									'tutorblock'
								) }
								help={ __(
									'YouTube/Vimeo links will open in a lightbox.',
									'tutorblock'
								) }
								value={ secondaryButtonUrl }
								onChange={ ( val ) =>
									setAttributes( {
										secondaryButtonUrl: val,
									} )
								}
							/>
						</>
					) }
				</PanelBody>

				<PanelBody
					title={ __( 'Layout & Sizing', 'tutorblock' ) }
					initialOpen={ false }
				>
					<RangeControl
						label={ __( 'Minimum Height (px)', 'tutorblock' ) }
						value={ minHeight }
						onChange={ ( val ) =>
							setAttributes( { minHeight: val } )
						}
						min={ 300 }
						max={ 1000 }
					/>
					<SelectControl
						label={ __( 'Content Width', 'tutorblock' ) }
						value={ contentWidth }
						options={ [
							{
								label: __( 'Narrow (560px)', 'tutorblock' ),
								value: 'narrow',
							},
							{
								label: __( 'Medium (720px)', 'tutorblock' ),
								value: 'medium',
							},
							{
								label: __( 'Wide (960px)', 'tutorblock' ),
								value: 'wide',
							},
							{
								label: __( 'Full width', 'tutorblock' ),
								value: 'full',
							},
						] }
						onChange={ ( val ) =>
							setAttributes( { contentWidth: val } )
						}
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Course Data (optional)', 'tutorblock' ) }
					initialOpen={ false }
				>
					<TextControl
						label={ __( 'TutorLMS Course ID', 'tutorblock' ) }
						help={ __(
							'Optionally link a course to auto-populate stats below the headline.',
							'tutorblock'
						) }
						type="number"
						value={ courseId }
						onChange={ ( val ) =>
							setAttributes( {
								courseId: parseInt( val, 10 ) || 0,
							} )
						}
					/>
					<ToggleControl
						label={ __(
							'Show course stats (students, rating)',
							'tutorblock'
						) }
						checked={ showCourseStats }
						onChange={ ( val ) =>
							setAttributes( { showCourseStats: val } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<ServerSideRender
					block="tutorblock/hero-banner"
					attributes={ attributes }
					LoadingResponsePlaceholder={ () => (
						<div className="tutorblock-loading">
							{ __( 'Loading Hero Banner…', 'tutorblock' ) }
						</div>
					) }
				/>
			</div>
		</>
	);
}
