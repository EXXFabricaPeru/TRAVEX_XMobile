import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SincronizacionPage } from './sincronizacion.page';

describe('SincronizacionPage', () => {
  let component: SincronizacionPage;
  let fixture: ComponentFixture<SincronizacionPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SincronizacionPage ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SincronizacionPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
