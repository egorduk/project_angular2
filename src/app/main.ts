import { bootstrap } from '@angular/platform-browser-dynamic';
import { disableDeprecatedForms, provideForms } from '@angular/forms';
import { AppComponent } from './app.component';
//import { APP_ROUTER_PROVIDERS } from './app.routes';
import { provide } from '@angular/core';
import { Http, HTTP_PROVIDERS } from '@angular/http';
import { provideRouter } from '@angular/router';
import { FORM_PROVIDERS } from '@angular/common';
//import { AuthConfig, AuthHttp } from 'angular2-jwt/angular2-jwt';
//import { AUTH_PROVIDERS } from 'angular2-jwt';
import { AUTH_PROVIDERS } from 'angular2-jwt/angular2-jwt';
import { AuthGuard } from './common/auth.guard';
import { routes } from './app.routes';

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

/*
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
 );*/

bootstrap(
    AppComponent,
    [
        provideRouter(routes),
        FORM_PROVIDERS,
        HTTP_PROVIDERS,
        AUTH_PROVIDERS,
        AuthGuard
    ]
);