
const requestStatusEvent = new CustomEvent('requestStatusChange', {
  detail: { active: false }
});

let activeRequests = 0;

let originalBodyOverflow;

function toggleBodyScroll(disable) {
  if (disable) {
    originalBodyOverflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = originalBodyOverflow || '';
  }
}

function updateRequestStatus(isActive) {
  if (isActive) {
    activeRequests++;
  } else {
    activeRequests--;
  }

  requestStatusEvent.detail.active = activeRequests > 0;
  document.dispatchEvent(requestStatusEvent);
}

// Monitor fetch requests
const originalFetch = window.fetch;
window.fetch = function (...args) {
  updateRequestStatus(true);

  return originalFetch.apply(this, args)
    .finally(() => {
      updateRequestStatus(false);
    });
};

// Monitor XMLHttpRequest
const originalXHR = window.XMLHttpRequest;
window.XMLHttpRequest = function () {
  const xhr = new originalXHR();

  xhr.addEventListener('loadstart', () => {
    updateRequestStatus(true);
  });

  xhr.addEventListener('loadend', () => {
    updateRequestStatus(false);
  });

  return xhr;
};

document.addEventListener('requestStatusChange', (event) => {
  const loadingIndicator = document.getElementById('loading-indicator');

  if (event.detail.active) {
    if (loadingIndicator) loadingIndicator.classList.remove('hidden');
    toggleBodyScroll(true);
  } else {
    if (loadingIndicator) loadingIndicator.classList.add('hidden');
    toggleBodyScroll(false);
  }
});