import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { DetallepagoPage } from './detallepago.page';

describe('DetallepagoPage', () => {
  let component: DetallepagoPage;
  let fixture: ComponentFixture<DetallepagoPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ DetallepagoPage ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(DetallepagoPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
