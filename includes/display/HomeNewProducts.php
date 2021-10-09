<?php



	CLASS ISC_HOMENEWPRODUCTS_PANEL extends PANEL

	{

		public $cacheable = true;

		public $cacheId = "products.homenewproducts";



		public function SetPanelSettings()

		{

			$count = 0;

			$output = "";

			$GLOBALS['SNIPPETS']['HomeNewProducts'] = '';



			if(GetConfig('HomeNewProducts') > 0) {

				if(!GetConfig('ShowProductRating')) {

					$GLOBALS['HideProductRating'] = "display: none";

				}



				$query = "

					SELECT p.*, FLOOR(prodratingtotal/prodnumratings) AS prodavgrating, pi.*, ".GetProdCustomerGroupPriceSQL()."

					FROM [|PREFIX|]products p

					LEFT JOIN [|PREFIX|]product_images pi ON (p.productid=pi.imageprodid AND pi.imageisthumb=1)

					WHERE p.prodvisible='1'

					".GetProdCustomerGroupPermissionsSQL()."

					ORDER BY proddateadded DESC

				";

				$query .= $GLOBALS['ISC_CLASS_DB']->AddLimit(0, GetConfig('HomeNewProducts'));



				$result = $GLOBALS['ISC_CLASS_DB']->Query($query);



				if($GLOBALS['ISC_CLASS_DB']->CountResult($result) > 0) {

					$GLOBALS['AlternateClass'] = '';

					while($row = $GLOBALS['ISC_CLASS_DB']->Fetch($result)) {



						if($GLOBALS['AlternateClass'] == 'Odd') {

							$GLOBALS['AlternateClass'] = 'Even';

						}

						else {

							$GLOBALS['AlternateClass'] = 'Odd';

						}



						$GLOBALS['ProductCartQuantity'] = '';

						if(isset($GLOBALS['CartQuantity'.$row['productid']])) {

							$GLOBALS['ProductCartQuantity'] = (int)$GLOBALS['CartQuantity'.$row['productid']];

						}



						$GLOBALS['ProductId'] = $row['productid'];

						$GLOBALS['ProductName'] = isc_html_escape($row['prodname']);

						$GLOBALS['ProductLink'] = ProdLink($row['prodname']);

						$GLOBALS['ProductRating'] = (int)$row['prodavgrating'];



						// Determine the price of this product

						$GLOBALS['ProductPrice'] = CalculateProductPrice($row);



						// Workout the product description

						$desc = strip_tags($row['proddesc']);



						if(strlen($desc) < 120) {

							$GLOBALS['ProductSummary'] = $desc;

						}

						else {

							$GLOBALS['ProductSummary'] = substr($desc, 0, 120) . "...";

						}



						$GLOBALS['ProductThumb'] = ImageThumb($row, ProdLink($row['prodname']));



						if (isId($row['prodvariationid']) || trim($row['prodconfigfields'])!='' || $row['prodeventdaterequired'] == 1) {

							$GLOBALS['ProductURL'] = ProdLink($row['prodname']);

							$GLOBALS['ProductAddText'] = GetLang('ProductChooseOptionLink');

						} else {

							$GLOBALS['ProductURL'] = CartLink($row['productid']);

							$GLOBALS['ProductAddText'] = GetLang('ProductAddToCartLink');

						}



						if (CanAddToCart($row) && GetConfig('ShowAddToCartLink')) {

						


$mos = GetModuleVariable('addon_parcelas','loginparapreco');
if($mos=='nao'){

$customerClass = GetClass('ISC_CUSTOMER');
if(!$customerClass->GetCustomerId()) {
$GLOBALS['HideActionAdd'] = "none;";
}else{
$GLOBALS['HideActionAdd'] = "";
}

}else{
$GLOBALS['HideActionAdd'] = '';
}


							$GLOBALS['Estoque'] = 'none';

						} else {

							$GLOBALS['HideActionAdd'] = 'none';

							$GLOBALS['Estoque'] = '';

						}



						$GLOBALS['HideProductVendorName'] = 'display: none';

						$GLOBALS['ProductVendor'] = '';

						if(GetConfig('ShowProductVendorNames') && $row['prodvendorid'] > 0) {

							$vendorCache = $GLOBALS['ISC_CLASS_DATA_STORE']->Read('Vendors');

							if(isset($vendorCache[$row['prodvendorid']])) {

								$GLOBALS['ProductVendor'] = '<a href="'.VendorLink($vendorCache[$row['prodvendorid']]).'">'.isc_html_escape($vendorCache[$row['prodvendorid']]['vendorname']).'</a>';

								$GLOBALS['HideProductVendorName'] = '';

							}

						}

                       

						//modificacao parcelamento

						$GLOBALS['FreteDestaques'] = FreteTipo($row['productid']);

						$GLOBALS['ProDestaques'] = simulador_de_rodape($row['productid']);

						$GLOBALS['SNIPPETS']['HomeNewProducts'] .= $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("HomeNewProductsItem");

					}



					// Showing the syndication option?

					if(GetConfig('RSSNewProducts') != 0 && GetConfig('RSSSyndicationIcons') != 0) {

						$GLOBALS['SNIPPETS']['HomeNewProductsFeed'] = $GLOBALS['ISC_CLASS_TEMPLATE']->GetSnippet("HomeNewProductsFeed");

					}

				}

				else {

					$this->DontDisplay = true;

					$GLOBALS['HideHomeNewProductsPanel'] = "none";

				}

			}

			else {

				$this->DontDisplay = true;

				$GLOBALS['HideHomeNewProductsPanel'] = "none";

			}

		}

	}