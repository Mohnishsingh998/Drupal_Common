(function () {

  const card = document.querySelector('.forecast-card');
  if (!card || !navigator.geolocation) return;

  // Show loading state
  card.classList.add('is-loading');

  navigator.geolocation.getCurrentPosition(
    function (position) {
      const { latitude, longitude } = position.coords;
      console.log(
        latitude,
        longitude
      )
      fetch(`/weather-widget/location?lat=${latitude}&lon=${longitude}`)
        .then(res => res.json())
        .then(data => {
          if (data.error) return;

          updateUI(card, data);
        })
        .catch(() => {
          console.log('Location fetch failed');
        })
        .finally(() => {
          card.classList.remove('is-loading');
        });
    },
    function () {
      // Permission denied → fallback automatically
      card.classList.remove('is-loading');
    }
  );

  function updateUI(card, data) {
    card.querySelector('.forecast-card__city').innerText = data.city;
    card.querySelector('.forecast-card__temp').innerText = data.temp + '°';
    card.querySelector('.forecast-card__updated').innerText = 'Updated ' + data.updated;

    card.querySelector('.forecast-card__meta').innerHTML = `
      <span>Humidity ${data.humidity}%</span>
      <span>Wind ${data.wind}</span>
      <span>Sunrise ${data.sunrise}</span>
      <span>Sunset ${data.sunset}</span>
    `;

    const img = card.querySelector('img');
    if (img) {
      img.src = `https://openweathermap.org/img/wn/${data.icon}@2x.png`;
    }

    // Update weather class
    card.className = `forecast-card is-${data.condition.toLowerCase()}`;
  }

})();