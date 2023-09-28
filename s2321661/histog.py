#!/localdisk/anaconda3/bin/python
import sys
# get sys package for file arguments etc
from sqlalchemy import create_engine
import pymysql
import numpy as np
import pandas as pd
import scipy.stats as sp
import matplotlib.pyplot as plt
import io

con = pymysql.connect(host='localhost', user='s2321661', passwd='asdfzxcv', db='s2321661')  # connect to databse
cur = con.cursor()  # create a cursor
if(len(sys.argv) != 4) :
    print ("Usage: histog.py col name where ; Nparams = ",sys.argv)
    sys.exit(-1)

col1 = sys.argv[1]  # accept arguments from php
col2 = sys.argv[3]
xname = sys.argv[2]
sql = "SELECT %s FROM Compounds where %s" % (col1,col2)
cur.execute(sql)  # run SQL Query
nrows = cur.rowcount
ds = cur.fetchall()  # retrieve data from query
ads = np.array(ds)
num_bins = 60  # set how many bins/rectangles in graph
title = "Distribution of " + xname
# the histogram of the data
n, bins, patches = plt.hist(ads, num_bins, density=False, facecolor='blue', alpha=0.5, edgecolor='white')
plt.xlabel(xname)
plt.ylabel('N')
plt.title(title)
image = io.BytesIO()
plt.savefig(image,format='png')
sys.stdout.buffer.write(image.getvalue())
#plt.show()
con.close()  # close connection
