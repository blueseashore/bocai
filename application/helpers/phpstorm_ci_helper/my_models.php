<?php die();

/**
 * Add you custom models here that you are loading in your controllers
 *
 * <code>
 * $this->site_model->get_records()
 * </code>
 * Where site_model is the model Class
 *
 * ---------------------- Models to Load ----------------------
 *
 * <warehouse>
 * @property menu_model $menu_model
 * @property role_model $role_model
 * @property user_model $user_model
 * @property user_role_model $user_role_model
 * @property role_menu_model $role_menu_model
 * @property warehouse_model $warehouse_model
 * @property warehouse_location_model $warehouse_location_model
 * @property warehouse_section_model $warehouse_section_model
 * @property warehouse_address_model $warehouse_address_model
 * @property transport_model $transport_model
 * @property transport_provider_model $transport_provider_model
 * @property transport_transfer_model $transport_transfer_model
 *
 * <product>
 * @property brand_model $brand_model
 * @property brand_series_model $brand_series_model
 * @property category_model $category_model
 * @property combo_item_model $combo_item_model
 * @property combo_model $combo_model
 * @property feature_model $feature_model
 * @property feature_value_model $feature_value_model
 * @property goods_model $goods_model
 * @property platform_item_model $platform_item_model
 * @property platform_listing_model $platform_listing_model
 * @property product_model $product_model
 *
 * <other>
 * @property city_model $city_model
 * @property country_model $country_model
 * @property currency_exchange_model $currency_exchange_model
 * @property currency_model $currency_model
 * @property fee_type_model $fee_type_model
 * @property keyword_model $keyword_model
 * @property platform_shop_model $platform_shop_model
 * @property poi_model $poi_model
 * @property province_model $province_model
 * @property shipping_cart_model $shipping_cart_model
 * @property supplier_model $supplier_model
 * @property supplier_product_model $supplier_product_model
 *
 * <order>
 * @property depot_out_model $depot_out_model
 * @property depot_out_product_model $depot_out_product_model
 * @property parcel_model $parcel_model
 * @property platform_order_item_model $platform_order_item_model
 * @property platform_order_model $platform_order_model
 * @property platform_sync_model $platform_sync_model
 * @property sales_order_model $sales_order_model
 * @property sales_order_product_model $sales_order_product_model
 *
 * <log>
 * @property fee_type_log_model $fee_type_log_model
 * @property upload_log_model $upload_log_model
 *
 */
class my_models
{
}

// End my_models.php
