if ( LI === undefined )
  var LI = {};
if ( LI.kiosk === undefined )
  LI.kiosk = [];

LI.kiosk.utils = [];
LI.kiosk.mustache = [];
LI.kiosk.manifestations = {};

$(document).ready(function(){
	LI.kiosk.utils.setupDialog();
	LI.kiosk.mustache.cacheTemplates();
  	LI.kiosk.getManifestations();

});

LI.kiosk.getManifestations = function(){
	LI.kiosk.utils.showLoader();

	$.ajax({
    url: '/tck_dev.php/transaction/getManifestations/action',
    type: 'GET',
    data: { simplified: 1 },
    dataType: 'json',
    success: LI.kiosk.insertManifestations,
    error: LI.kiosk.utils.error
  });
};

LI.kiosk.insertManifestations = function(data){
	var type = 'manifestations';
	var cardTemplate = $('#manif-card-template').html();

	$.each(data.success.success_fields[type].data.content, function(key, manif){
		
		if ( window.location.hash == '#debug' )
			console.error('Loading an item (#' + manif.id + ') from the ' + type);

		var pdt;
		switch ( type ) {
			case 'museum':
			case 'manifestations':
				var manifDate = new Date(manif.happens_at.replace(' ', 'T'));
				pdt = manifDate.toLocaleString().replace(/:\d\d( \w+){0,1}$/,'');
				break;
			case 'store':
				pdt = manif.name;
				break;
			default:
				pdt = '';
				break;
		}

		//re arrange properties
		manif.name = manif.name == null ? manif.category : manif.name;
		manif.start = pdt;
		var manifEnd = new Date(manif.ends_at.replace(' ', 'T')).getTime();
		manif.end = manif.ends_at.split(' ')[1];
		
		for(key in manif.gauges)	
			if( manif.gauges[key].name == 'INDIVIDUELS' )
				manif.prices = manif.gauges[key].available_prices;

		//add manif to global variable for futur retrieval
		LI.kiosk.manifestations[manif.id] = manif;

		//Render and insert card
  		var card = Mustache.render(cardTemplate, { manif: manif });
  		$('#manifs-list').append(card);
	});

	LI.kiosk.addManifListener();
	LI.kiosk.utils.hideLoader();
};

LI.kiosk.utils.error = function(error){
	console.error(error);
	$('#spinner').removeClass('is-active');
};

LI.kiosk.utils.showLoader = function(){
	$('#spinner').addClass('is-active');
	$('#spinner').css('display', 'block');
};

LI.kiosk.utils.hideLoader = function(){
	$('#spinner').removeClass('is-active');
	$('#spinner').css('display', 'none');
};

LI.kiosk.utils.setupDialog = function(){
	LI.kiosk.utils.dialog = document.querySelector('dialog');
	if (! LI.kiosk.utils.dialog.showModal)
      dialogPolyfill.registerDialog(LI.kiosk.utils.dialog);
};

LI.kiosk.addManifListener = function(){
	$('#manifs-list').on('click', '.manif', function(event){
		LI.kiosk.utils.showLoader();
		var manif = LI.kiosk.manifestations[$(event.currentTarget.children).attr('id')];
	  	LI.kiosk.openOrderDialog(manif);
	});
}

LI.kiosk.mustache.cacheTemplates = function(){
	var templates = ['manif-card-template'];
	//make mustache cache the templates for quicker future uses
	$('script[type="x-tmpl-mustache"]').each(function(id, template){
		Mustache.parse($(template).html());
	});
};

LI.kiosk.openOrderDialog = function(manif){
	var dialogTemplate = $('#manif-dialog-template').html();

	$('dialog').html(Mustache.render(dialogTemplate, { manif: manif }));
	LI.kiosk.addPrices(manif.prices);

	LI.kiosk.utils.dialog.showModal();
		
	$('.close, .backdrop').click(function(){
		LI.kiosk.utils.dialog.close();
	});
};

LI.kiosk.addPrices = function(prices){
	var priceTemplate = $('#price-widget-template').html();

	for(key in prices)
		$('dialog #prices').append(Mustache.render(priceTemplate, { price: prices[key] }));
};

