import { bootstrap } from '@angular/platform-browser-dynamic';
import { disableDeprecatedForms, provideForms } from '@angular/forms';
import { AppComponent } from './app.component';
import { provide } from '@angular/core';
import { Http, HTTP_PROVIDERS } from '@angular/http';
import { provideRouter } from '@angular/router';
import { AUTH_PROVIDERS } from 'angular2-jwt/angular2-jwt';
import { AuthGuard } from './common/auth.guard';
import { routes } from './app.routes';

bootstrap(
    AppComponent,
    [
        provideRouter(routes),
        disableDeprecatedForms(),
        provideForms(),
       // FORM_PROVIDERS,
        HTTP_PROVIDERS,
        AUTH_PROVIDERS,
        AuthGuard
    ]
);