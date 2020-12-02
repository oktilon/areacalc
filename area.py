# -*- coding: utf-8 -*-
# вычисление площади сфероидического полигона
from sys import argv
import math
import sph
import auth
# import numpy

script, fname = argv

a, f = 6378137, 1./298.257223563 # большая полуось и сжатие

print "atan2(10,12)   = %.16f" %(math.atan2(10, 12))
print "atan2(-10,12)   = %.16f" %(math.atan2(-10, 12))
print "atan2(10,-12)   = %.16f" %(math.atan2(10, -12))
print "atan2(-10,-12)   = %.16f" %(math.atan2(-10, -12))
exit


# инициализировать эквивалентную сферу
r_auth, to_auth_2, to_auth_4, to_auth_6 = auth.init(a, f)

print "SPH R=%.16f, a2=%.16f, a4=%.16f, a6=%.16f" %(r_auth, to_auth_2, to_auth_4, to_auth_6)

fp = open(fname, 'r')
tau = 0.
azi0 = azi1 = azi2 = 0.
i = 1
for line in fp:
    # прочитать долготу и широту
    alon, alat = map(float, line.split(" "))
    lon = math.radians(alon)
    lat = math.radians(alat)
    lonx = lon
    latx = lat

    # вычислить эквивалентную широту
    lat = auth.trigSeries(lat, to_auth_2, to_auth_4, to_auth_6)
    if i > 1:
        # вычислить прямой азимут Qi - Qi+1
        dist, azi1 = sph.inverse(lat1, lon1, lat, lon)
        if i == 2:
            # запомнить азимут Q1 - Q2
            azi0 = azi1
        else:
            # вычислить поворот в i-й вершине
            tau_i = 0.5 - (azi2 - azi1) / 2. / math.pi
            # нормализовать величину поворота
            tau_i = tau_i - math.floor(tau_i + 0.5)
            # добавить поворот к сумме поворотов
            tau = tau + tau_i
        # вычислить обратный азимут Qi+1 - Qi
        dist, azi2 = sph.inverse(lat, lon, lat1, lon1)
    lon1, lat1 = lon, lat
    print "%03d=%.16f %.16f, r=%.16f %.16f, l=%.16f, t=%.16f, a0=%.16f, a1=%.16f, a2=%.16f" %(i,alon, alat, lonx, latx, lat, tau, azi0, azi1, azi2)
    i = i + 1
fp.close()

# вычислить поворот в 1-й вершине
tau_i = 0.5 - (azi2 - azi0) / 2. / math.pi
# нормализовать величину поворота
tau_i = tau_i - math.floor(tau_i + 0.5)
# добавить поворот к сумме поворотов
tau = tau + tau_i

# вычислить площадь
area = 2. * math.pi * (1. - math.fabs(tau)) * r_auth ** 2
print "%g" % area
