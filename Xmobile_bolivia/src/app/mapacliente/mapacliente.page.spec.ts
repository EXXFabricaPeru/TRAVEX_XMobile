import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MapaclientePage } from './mapacliente.page';

describe('MapaclientePage', () => {
  let component: MapaclientePage;
  let fixture: ComponentFixture<MapaclientePage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MapaclientePage ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MapaclientePage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
