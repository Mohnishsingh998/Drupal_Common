(function () {

  const card = document.querySelector('.forecast-card');
  if (!card) return;

  card.classList.add('is-loading');
  if (navigator.geolocation) {

    navigator.geolocation.getCurrentPosition(
      function (position) {
        const { latitude, longitude } = position.coords;

        console.log("Geo:", latitude, longitude);

        // Current weather
        fetch(`/weather-widget/location?lat=${latitude}&lon=${longitude}`)
          .then(res => res.json())
          .then(data => {
            if (!data.error) updateUI(card, data);
          })
          .catch(() => console.log('Location fetch failed'))
          .finally(() => card.classList.remove('is-loading'));

        // Weekly forecast
        fetch(`/weather-widget/weekly?lat=${latitude}&lon=${longitude}`)
          .then(res => res.json())
          .then(days => {
            console.log("Weekly (geo):", days);
            renderWeekly(days);
          })
          .catch(() => console.log('Weekly forecast fetch failed'));
      },

      function () {
        console.log("Location denied → using city fallback");

        card.classList.remove('is-loading');

        const city = document
          .querySelector('.forecast-card__city')
          ?.innerText.trim();

        if (!city) return;

        fetch(`/weather-widget/weekly-city?city=${encodeURIComponent(city)}`)
          .then(res => res.json())
          .then(days => {
            console.log("Weekly (city):", days);
            renderWeekly(days);
          })
          .catch(() => console.log('Fallback weekly failed'));
      }
    );

  } else {
    console.log("No geolocation → using city fallback");

    const city = document
      .querySelector('.forecast-card__city')
      ?.innerText.trim();

    if (!city) return;

    fetch(`/weather-widget/weekly-city?city=${encodeURIComponent(city)}`)
      .then(res => res.json())
      .then(days => renderWeekly(days));
  }

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

    card.className = `forecast-card is-${data.condition.toLowerCase()}`;
  }
  function renderWeekly(days) {
    const container = document.querySelector('.forecast-weekly');
    if (!container) return;

    if (!days.length) {
      container.innerHTML = '<span>No forecast available</span>';
      return;
    }

    container.innerHTML = days.map(day => `
      <div class="forecast-day">
        <span>${day.day}</span>
        <img src="https://openweathermap.org/img/wn/${day.icon}.png" alt="${day.day}">
        <span>${day.temp}°</span>
      </div>
    `).join('');
  }

})();