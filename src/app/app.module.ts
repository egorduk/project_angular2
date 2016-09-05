import { NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';

import { routing } from './app.routes';

import { AppComponent }  from './app.component';

import { DataService } from './common/service/data.service';
import { AlertComponent } from 'ng2-bootstrap/components/alert';
import { AUTH_PROVIDERS } from 'angular2-jwt/angular2-jwt';

import { FILE_UPLOAD_DIRECTIVES } from 'ng2-file-upload/ng2-file-upload';
import { SafeBgPipe } from './common/pipe/safe.pipe';
import { FileSizePipe } from './common/pipe/fileSize.pipe';
import { SafeFileExtPipe } from './common/pipe/safe.pipe';
import { SELECT_DIRECTIVES } from 'ng2-select/ng2-select';
import { LoginComponent } from './login/login.component';
import { ModalModule } from 'ng2-bootstrap/components/modal';

@NgModule({
    imports: [ BrowserModule, routing, FormsModule, HttpModule, ModalModule ],
    declarations: [ AppComponent, SafeBgPipe, AlertComponent, FileSizePipe, SafeFileExtPipe, FILE_UPLOAD_DIRECTIVES, SELECT_DIRECTIVES, LoginComponent ],
    bootstrap: [ AppComponent ],
    providers: [ DataService, AUTH_PROVIDERS ]
})
export class AppModule { }