#!/localdisk/anaconda3/bin/python
import sys
# get sys package for file arguments etc
import pymysql
import numpy as np
import scipy.stats as sp
con = pymysql.connect(host='localhost', user='s2321661', passwd='asdfzxcv', db='s2321661')  # connect to databse
cur = con.cursor()  # create a cursor
if(len(sys.argv) != 4) :
  print ("Usage: correlate.py col1 col2 (selection); Nparams = ",sys.argv)
  sys.exit(-1)

col1 = sys.argv[1]  # accept arguments from php
col2 = sys.argv[2]
sel  = sys.argv[3]
sql = "SELECT %s,%s FROM Compounds where %s" % (col1,col2,sel)
cur.execute(sql)   # run SQL Query
nrows = cur.rowcount
ds = cur.fetchall()  # retrieve data from query
ads = np.array(ds)
print("<b>Pearson Correlation:</b>",sp.pearsonr(ads[:,0],ads[:,1]))
print("<br><b>Data Amount:</b>",nrows)
con.close()  # close connection
