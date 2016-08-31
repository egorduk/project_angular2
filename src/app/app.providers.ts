import { bind } from '@angular/core';
import { HTTP_PROVIDERS } from '@angular/http';
import { LocationStrategy, HashLocationStrategy } from '@angular/common';
import { Sorter } from './shared/utils/sorter';
import { DataService } from './common/service/data.service';
import { MessageService } from './common/service/message.service';
import { GlobalService } from './common/service/global.service';
import { TrackByService } from './shared/services/trackby.service';
import { FocusDirective } from './common/directive/focus.directive';
import { CheckGalleryDirective } from './common/directive/checkGallery.directive';

export const APP_PROVIDERS = [
    Sorter,
    DataService,
    TrackByService,
    HTTP_PROVIDERS,
    GlobalService,
    FocusDirective,
    CheckGalleryDirective,
    MessageService
];