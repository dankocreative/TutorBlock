import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	SelectControl,
	ColorPicker,
	TextControl,
	TextareaControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit( { attributes, setAttributes } ) {
	const {
		courseId,
		headline,
		subheadline,
		buttonText,
		buttonUrl,
		showCourseThumbnail,
		showCourseTitle,
		showPrice,
		showStats,
		showRating,
		showMoneyBack,
		moneyBackText,
		layout,
		primaryColor,
		buttonTextColor,
		backgroundStyle,
	} = attributes;

	const blockProps = useBlockProps( {
		className: 'tutorblock-cta-wrapper',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Course & Link', 'tutorblock' ) }
					initialOpen={ true }
				>
					<TextControl
						label={ __( 'Course ID (optional)', 'tutorblock' ) }
						help={ __(
							'Link to a specific TutorLMS course. Leave 0 to use a custom URL below.',
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
					<TextControl
						label={ __( 'Custom Button URL', 'tutorblock' ) }
						help={ __( 'Used when Course ID is 0.', 'tutorblock' ) }
						value={ buttonUrl }
						onChange={ ( val ) =>
							setAttributes( { buttonUrl: val } )
						}
						type="url"
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Copy / Text', 'tutorblock' ) }
					initialOpen={ false }
				>
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
						rows={ 3 }
					/>
					<TextControl
						label={ __( 'Button Text', 'tutorblock' ) }
						value={ buttonText }
						onChange={ ( val ) =>
							setAttributes( { buttonText: val } )
						}
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Display Options', 'tutorblock' ) }
					initialOpen={ false }
				>
					<ToggleControl
						label={ __( 'Show Course Thumbnail', 'tutorblock' ) }
						checked={ showCourseThumbnail }
						onChange={ ( val ) =>
							setAttributes( { showCourseThumbnail: val } )
						}
					/>
					<ToggleControl
						label={ __( 'Show Course Title', 'tutorblock' ) }
						checked={ showCourseTitle }
						onChange={ ( val ) =>
							setAttributes( { showCourseTitle: val } )
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
						label={ __( 'Show Course Stats', 'tutorblock' ) }
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
						label={ __(
							'Show Money-Back Guarantee',
							'tutorblock'
						) }
						checked={ showMoneyBack }
						onChange={ ( val ) =>
							setAttributes( { showMoneyBack: val } )
						}
					/>
					{ showMoneyBack && (
						<TextControl
							label={ __( 'Guarantee Text', 'tutorblock' ) }
							value={ moneyBackText }
							onChange={ ( val ) =>
								setAttributes( { moneyBackText: val } )
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
									'Horizontal (two-column)',
									'tutorblock'
								),
								value: 'horizontal',
							},
							{
								label: __(
									'Vertical (centered)',
									'tutorblock'
								),
								value: 'vertical',
							},
							{
								label: __(
									'Inline (compact bar)',
									'tutorblock'
								),
								value: 'inline',
							},
						] }
						onChange={ ( val ) => setAttributes( { layout: val } ) }
					/>
					<SelectControl
						label={ __( 'Background Style', 'tutorblock' ) }
						value={ backgroundStyle }
						options={ [
							{
								label: __( 'White / Light', 'tutorblock' ),
								value: 'white',
							},
							{
								label: __( 'Dark', 'tutorblock' ),
								value: 'dark',
							},
							{
								label: __( 'Primary Color', 'tutorblock' ),
								value: 'primary',
							},
							{
								label: __( 'Gradient', 'tutorblock' ),
								value: 'gradient',
							},
						] }
						onChange={ ( val ) =>
							setAttributes( { backgroundStyle: val } )
						}
					/>
				</PanelBody>

				<PanelBody
					title={ __( 'Colors', 'tutorblock' ) }
					initialOpen={ false }
				>
					<p className="components-base-control__label">
						{ __( 'Button / Primary Color', 'tutorblock' ) }
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
			</InspectorControls>

			<div { ...blockProps }>
				<div className="tutorblock-editor-label">
					<span className="tutorblock-editor-badge">
						🎯 { __( 'TutorBlock: Enrollment CTA', 'tutorblock' ) }
					</span>
				</div>
				<ServerSideRender
					block="tutorblock/enrollment-cta"
					attributes={ attributes }
					httpMethod="POST"
				/>
			</div>
		</>
	);
}
