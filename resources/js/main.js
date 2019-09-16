// used by trade-offer page
// returns true if any offer exists
function offerExists(offerObj) {
  if (offerObj.players.length || offerObj.credits) {
    return true;
  }
  return false;
}

$(document).ready(function () {
  $('#close-status').on('click', function () {
    $('#status-alert').hide();
    return false;
  });

  // get current URL
  const url = window.location.pathname;
  // filename without extension
  const filename = url.match(/([^/]+)(?=\.\w+$)/)[0];
  // add the 'active' link class to the nav item for the current page
  $(`#${filename}`).addClass('active');

  // only run this on the trade-offer page
  if (filename === 'trade-offer') {
    // object representing the current offer
    const offer = {
      players: [],
      credits: 0
    };
    // trade-offer page ui/ux
    $('.new-offer').hide();
    $('.remove-from-offer').hide();
    $('.offer-credits').hide();
    // adding credits to the bid
    $('.add-credits').on('submit', function () {
      const credits = $(this).find('input[name="credits"]').val();
      offer.credits = parseInt(credits, 10);
      $('.offer-credits').show();
      // show the current offer section
      $('.new-offer').show();
      // update visual to show credit offer
      $('.credits-value').text(offer.credits);
      if (offer.credits === 1) {
        $('.credits-text').text('EuroBuck');
      } else {
        $('.credits-text').text('EuroBucks');
      }
      // reset the form
      $(this)[0].reset();
      // update value to submit as offer
      $('.submit-offer').find('input[name="credits"]').val(offer.credits);
      return false;
    });
    // Removing credits from the bid
    $('.remove-credits').on('submit', function () {
      offer.credits = 0;
      $('.offer-credits').hide();
      // hide the new offer section if there is no offer
      if (!offerExists(offer)) {
        $('.new-offer').hide();
      }
      // update value to submit as offer
      $('.submit-offer').find('input[name="credits"]').val(offer.credits);
      return false;
    });
    // clicking the plus, move the player card from available to in-progress offer
    $('.add-to-offer').on('click', function () {
      // don't allow more than 5 players to be a part of a deal
      if (offer.players.length <= 5) {
        // get the player's position
        const position = $(this)
          .parent()
          .parent('.well')
          .attr('data-position')
          .toLowerCase();
        const id = $(this)
          .parent()
          .parent('.well')
          .attr('data-id');
        offer.players.push(id);
        // hide all of the plus signs if we hit 5 players
        if (offer.players.length >= 5) {
          $('.add-to-offer').hide();
        }
        // move this card from available to current offer
        const card = $(this)
          .parent()
          .parent('.well')
          .parent('.trade-player-card')
          .detach();
        $('.new-offer').find(`.position-${position}`).append(card);
        // show the current offer section
        $('.new-offer').show();
        // hide this plus sign, show the minues sign
        $(this).hide();
        $(this).siblings($('.remove-from-offer')).show();
        // update value to submit as offer
        $('.submit-offer').find('input[name="players"]').val(offer.players);
      }
      return false;
    });
    // click the minus, remove from in-progress to available assets
    $('.remove-from-offer').on('click', function () {
      // get the player's position
      const position = $(this)
        .parent()
        .parent('.well')
        .attr('data-position')
        .toLowerCase();
      // remove this player from the current offer object
      const id = $(this)
        .parent()
        .parent('.well')
        .attr('data-id');
      // find the player in the object
      const index = offer.players.indexOf(id);
      // remove the player
      offer.players.splice(index, 1);
      // if fewer than 5 players are being offered, show the plus signs
      if (offer.players.length <= 5) {
        $('.add-to-offer').show();
      }
      // move this card from available to current offer
      const card = $(this)
        .parent()
        .parent('.well')
        .parent('.trade-player-card')
        .detach();
      $('.tradable-assets').find(`.position-${position}`).append(card);
      // hide this minus sign, show the plus sign
      $(this).hide();
      $(this).siblings($('.add-from-offer')).show();
      // hide the new offer section if there is no offer
      if (!offerExists(offer)) {
        $('.new-offer').hide();
      }
      // update value to submit as offer
      $('.submit-offer').find('input[name="players"]').val(offer.players);
      return false;
    });
  }
});
