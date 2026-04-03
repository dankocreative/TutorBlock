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
		videoUrl,
		thumbnailUrl,
		thumbnailId,
		courseId,
		title,
		description,
		showOverlayButton,
		overlayButtonText,
		overlayButtonUrl,
		overlayButtonColor,
		overlayButtonTextColor,
		showPlayButton,
		playButtonColor,
		aspectRatio,
		overlayColor,
		overlayOpacity,
		captionPosition,
		borderRadius,
		autoplayOnClick,
	} = attributes;

	const blockProps = useBlockProps( {
		className: 'tutorblock-video-preview-wrapper',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Video & Thumbnail', 'tutorblock' ) }
					initialOpen={ true }
				>
					<TextControl
						label={ __( 'Video URL', 'tutorblock' ) }
						help={ __(
							'YouTube, Vimeo, or direct .mp4/.webm URL. Used when the play button is clicked.',
							'tutorblock'
						) }
						value={ videoUrl }
						onChange={ ( val ) =>
							setAttributes( { videoUrl: val } )
						}
					/>

					<MediaUploadCheck>
						<MediaUpload
							onSelect={ ( media ) =>
								setAttributes( {
									thumbnailUrl: media.url,
									thumbnailId: media.id,
								} )
							}
							allowedTypes={ [ 'image' ] }
							value={ thumbnailId }
							render={ ( { open } ) => (
								<div>
									<p
										style={ {
											margin: '0 0 6px',
											fontSize: '12px',
											fontWeight: 600,
										} }
									>
										{ __(
											'Custom Thumbnail',
											'tutorblock'
										) }
									</p>
									{ thumbnailUrl && (
										<img
											src={ thumbnailUrl }
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
										{ thumbnailUrl
											? __(
													'Change Thumbnail',
													'tutorblock'
											  )
											: __(
													'Select Thumbnail',
													'tutorblock'
											  ) }
									</Button>
									{ thumbnailUrl && (
										<Button
											onClick={ () =>
												setAttributes( {
													thumbnailUrl: '',
													thumbnailId: 0,
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
						label={ __( 'TutorLMS Course ID', 'tutorblock' ) }
						help={ __(
							'Auto-uses course thumbnail if no custom thumbnail is set.',
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
				</PanelBody>

				<PanelBody
					title={ __( 'Play Button & Overlay', 'tutorblock' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __( 'Show Play Button', 'tutorblock' ) }
						checked={ showPlayButton }
						onChange={ ( val ) =>
							setAttributes( { showPlayButton: val } )
						}
					/>
					{ showPlayButton && (
						<>
							<p
								style={ {
									margin: '0 0 4px',
									fontSize: '11px',
									fontWeight: 600,
									textTransform: 'uppercase',
								} }
							>
								{ __( 'Play Button Color', 'tutorblock' ) }
							</p>
							<ColorPicker
								color={ playButtonColor }
								onChange={ ( val ) =>
									setAttributes( {
										playButtonColor: val,
									} )
								}
								enableAlpha={ false }
							/>
						</>
					) }

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
						max={ 80 }
					/>

					<ToggleControl
						label={ __( 'Autoplay video on click', 'tutorblock' ) }
						checked={ autoplayOnClick }
						onChange={ ( val ) =>
							setAttributes( { autoplayOnClick: val } )
						}
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Overlaid Sign-Up Button', 'tutorblock' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __(
							'Show sign-up button on image',
							'tutorblock'
						) }
						checked={ showOverlayButton }
						onChange={ ( val ) =>
							setAttributes( { showOverlayButton: val } )
						}
					/>
					{ showOverlayButton && (
						<>
							<TextControl
								label={ __( 'Button Text', 'tutorblock' ) }
								value={ overlayButtonText }
								onChange={ ( val ) =>
									setAttributes( {
										overlayButtonText: val,
									} )
								}
							/>
							<TextControl
								label={ __( 'Button URL', 'tutorblock' ) }
								value={ overlayButtonUrl }
								onChange={ ( val ) =>
									setAttributes( {
										overlayButtonUrl: val,
									} )
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
								color={ overlayButtonColor }
								onChange={ ( val ) =>
									setAttributes( {
										overlayButtonColor: val,
									} )
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
								color={ overlayButtonTextColor }
								onChange={ ( val ) =>
									setAttributes( {
										overlayButtonTextColor: val,
									} )
								}
								enableAlpha={ false }
							/>
						</>
					) }
				</PanelBody>

				<PanelBody
					title={ __( 'Caption & Text', 'tutorblock' ) }
					initialOpen={ false }
				>
					<TextControl
						label={ __( 'Video Title', 'tutorblock' ) }
						value={ title }
						onChange={ ( val ) => setAttributes( { title: val } ) }
					/>
					<TextareaControl
						label={ __( 'Description', 'tutorblock' ) }
						value={ description }
						onChange={ ( val ) =>
							setAttributes( { description: val } )
						}
					/>
					<SelectControl
						label={ __( 'Caption / Text Position', 'tutorblock' ) }
						value={ captionPosition }
						options={ [
							{
								label: __( 'Below video', 'tutorblock' ),
								value: 'below',
							},
							{
								label: __( 'Overlaid (bottom)', 'tutorblock' ),
								value: 'overlay-bottom',
							},
							{
								label: __( 'Overlaid (center)', 'tutorblock' ),
								value: 'overlay-center',
							},
							{
								label: __( 'Hidden', 'tutorblock' ),
								value: 'none',
							},
						] }
						onChange={ ( val ) =>
							setAttributes( { captionPosition: val } )
						}
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Dimensions & Style', 'tutorblock' ) }
					initialOpen={ false }
				>
					<SelectControl
						label={ __( 'Aspect Ratio', 'tutorblock' ) }
						value={ aspectRatio }
						options={ [
							{ label: '16:9 (widescreen)', value: '16/9' },
							{ label: '4:3 (standard)', value: '4/3' },
							{ label: '21:9 (cinematic)', value: '21/9' },
							{ label: '1:1 (square)', value: '1/1' },
						] }
						onChange={ ( val ) =>
							setAttributes( { aspectRatio: val } )
						}
					/>
					<RangeControl
						label={ __( 'Border Radius (px)', 'tutorblock' ) }
						value={ borderRadius }
						onChange={ ( val ) =>
							setAttributes( { borderRadius: val } )
						}
						min={ 0 }
						max={ 32 }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<ServerSideRender
					block="tutorblock/video-preview"
					attributes={ attributes }
					LoadingResponsePlaceholder={ () => (
						<div className="tutorblock-loading">
							{ __( 'Loading Video Preview…', 'tutorblock' ) }
						</div>
					) }
				/>
			</div>
		</>
	);
}
