import http from "k6/http";
import { check } from "k6";

export const options = {
    scenarios: {
        two_requests_at_once: {
            executor: "constant-vus",
            vus: 2,
            duration: "1s",
        },
    },
};

export default function () {
    const payload = JSON.stringify({
        name: "Gergo",
        email: `gergo-${__VU}@gmail.com`,
        hairdresser_id: 3,
        date: "2026-07-06",
        hour: "10:00",
    });

    const params = {
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
        },
    };

    const response = http.post(
        "http://127.0.0.1:8000/api/bookings",
        payload,
        params,
    );

    check(response, {
        "status is 201 or 409 or 422": (r) =>
            r.status === 201 || r.status === 409 || r.status === 422,
    });

    console.log(
        `VU ${__VU}: status ${response.status}, body: ${response.body}`,
    );
}
