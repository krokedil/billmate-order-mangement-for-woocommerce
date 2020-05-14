<?php // phpcs:ignore/** * Helper product class *//** * This is the class just for testing purpose * * @package Krokedil/tests */class Krokedil_Variable_Product extends AKrokedil_WC_Product {	/**	 * Products attributes	 *	 * @var WC_Product_Attribute[] $attributes	 */	protected $attributes;	/**	 * Products	 *	 * @var WC_Product_Variation[] $variations	 */	protected $variations;	/**	 * Krokedil_Variable_Product constructor.	 *	 * @param array                  $data props.	 * @param WC_Product_Attribute[] $attributes attributes.	 * @param WC_Product_Variation[] $variations variations.	 */	public function __construct( array $data = [], $attributes = [], $variations = [] ) {		parent::__construct( $data );		$this->set_attributes( $attributes );		$this->set_variations( $variations );	}	/**	 * Indicate whether to save or not.	 *	 * @return bool	 */	public function save(): bool {		return true;	}	/**	 * Creates attributes if are $attributes is empty	 *	 * @param array $attributes attributes.	 */	private function set_attributes( $attributes ) {		if ( empty( $attributes ) ) {			$size                = ( new Krokedil_Product_Attribute(				'size',				[					'small',					'large',					'huge',				]			) )->get_product_attribute();			$colour              = ( new Krokedil_Product_Attribute(				'colour',				[					'red',					'blue',					'green',				]			) )->get_product_attribute();			$this->attributes [] = $size;			$this->attributes [] = $colour;		} else {			$this->attributes = $attributes;		}	}	/**	 * Sets variations	 *	 * @param WC_Product_Variation[] $variations product variations.	 */	public function set_variations( $variations ) {		if ( empty( $variations ) ) {			$variation_one    = ( new Krokedil_Variation_Product(				[					'sku'           => 'DUMMY SKU VARIABLE SMALL',					'regular_price' => 10,				]			) )->create();			$this->variations = $variation_one;		} else {			$this->variations = $variations;		}	}	/**	 * Creates a grouped product	 *	 * @return WC_Product_Variable product.	 */	public function create(): WC_Product {		$wc_product = new WC_Product_Variable();		$wc_product->set_props( $this->get_data() );		$wc_product->set_attributes( $this->attributes );		foreach ( $this->variations as $variation ) {			/**			 * Product variation			 *			 * @var WC_Product_Variation $variation			 */			$variation->set_parent_id( $wc_product->get_id() );			$variation->save();		}		if ( $this->save() ) {			$wc_product->save();			return wc_get_product( $wc_product );		}		return $wc_product;	}}