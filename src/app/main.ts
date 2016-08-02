import { bootstrap } from '@angular/platform-browser-dynamic';
import { disableDeprecatedForms, provideForms } from '@angular/forms';
import { AppComponent } from './app.component';
import { APP_ROUTER_PROVIDERS } from './app.routes';
//import {LocalStorageService, LocalStorageSubscriber} from 'angular2-localstorage/LocalStorageEmitter';


import { provide } from '@angular/core';
import { Http, HTTP_PROVIDERS } from '@angular/http';
import { AuthConfig, AuthHttp } from 'angular2-jwt/angular2-jwt';

/*var appPromise = bootstrap(AppComponent,[
    APP_ROUTER_PROVIDERS,
    disableDeprecatedForms(),
    provideForms(),
    LocalStorageService
])
.then(
    success => console.log('AppComponent bootstrapped!'),
    error => console.log(error)
);

LocalStorageSubscriber(appPromise);*/

bootstrap(
    AppComponent,
    [
        disableDeprecatedForms(),
        provideForms(),
        APP_ROUTER_PROVIDERS,
        HTTP_PROVIDERS,
        provide(AuthHttp, {
            useFactory: (http) => {
                return new AuthHttp(new AuthConfig({
                    tokenName: 'jwt'
                }), http);
            },
            deps: [Http]
        })
    ]
);

https://auth0.com/blog/creating-your-first-real-world-angular-2-app-from-authentication-to-calling-an-api-and-everything-in-between/